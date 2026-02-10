<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBulkNilaiRequest;
use App\Http\Requests\ImportExcelRequest;
use App\Services\NilaiService;
use App\Services\ImportService;
use App\Services\RaporService;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RaporController extends Controller
{
    private NilaiService $nilaiService;
    private ImportService $importService;
    private RaporService $raporService;

    public function __construct(
        NilaiService $nilaiService,
        ImportService $importService,
        RaporService $raporService
    ) {
        $this->nilaiService = $nilaiService;
        $this->importService = $importService;
        $this->raporService = $raporService;
        $this->middleware('auth');
    }

    /**
     * Dashboard wali kelas - lihat kelas yang dipandu
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Hanya wali kelas yang boleh akses
        if (!$user->hasRole('wali_kelas')) {
            abort(403, 'Unauthorized');
        }

        $kelas = $user->kelasAsWaliKelas()->with('siswa')->get();
        $tahunAjarans = TahunAjaran::all();

        return view('wali_kelas.dashboard', compact('kelas', 'tahunAjarans'));
    }

    /**
     * List rapor untuk kelas wali
     */
    public function listRapor(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('wali_kelas')) {
            abort(403, 'Unauthorized');
        }

        $kelasList = $user->kelasAsWaliKelas()->get();
        $tahunAjarans = TahunAjaran::all();

        // Jika belum pilih tahun ajaran, tampilkan form filter
        if (!$request->filled('tahun_ajaran_id')) {
            return view('wali_kelas.rapor.list', compact('kelasList', 'tahunAjarans'));
        }

        $validated = $request->validate([
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
        ]);

        // Ambil siswa dari kelas-kelas yang dipandu
        $siswa = Siswa::whereIn('kelas_id', $user->kelasAsWaliKelas()->pluck('id'))
            ->with(['kelas', 'nilai' => function ($query) use ($validated) {
                $query->where('tahun_ajaran_id', $validated['tahun_ajaran_id']);
            }])
            ->get();

        $tahunAjaran = TahunAjaran::find($validated['tahun_ajaran_id']);

        return view('wali_kelas.rapor.list', compact('siswa', 'tahunAjaran', 'kelasList', 'tahunAjarans'));
    }

    /**
     * View detail rapor siswa
     */
    public function viewRapor($siswaId, $tahunAjaranId)
    {
        $user = Auth::user();
        $siswa = Siswa::findOrFail($siswaId);

        // Check if siswa belongs to one of wali kelas's kelas
        if (!$user->kelasAsWaliKelas()->whereId($siswa->kelas_id)->exists()) {
            abort(403, 'Anda tidak berhak mengakses rapor siswa ini');
        }

        $data = $this->raporService->getRaporData($siswaId, $tahunAjaranId);
        return view('wali_kelas.rapor.view', compact('data'));
    }

    /**
     * Cetak rapor 1 siswa — semua mapel di kelasnya
     */
    public function downloadRapor($siswaId, $tahunAjaranId)
    {
        $user = Auth::user();
        $siswa = Siswa::with('kelas.jurusan')->findOrFail($siswaId);

        if (!$user->kelasAsWaliKelas()->whereId($siswa->kelas_id)->exists()) {
            abort(403, 'Unauthorized');
        }

        try {
            $kelas = $siswa->kelas;
            $tahunAjaran = TahunAjaran::findOrFail($tahunAjaranId);

            // Ambil mapel yang terdaftar di kelas ini dari pivot
            $mapels = $kelas->mataPelajarans()->orderBy('nama_mapel')->get();

            // Jika belum ada di pivot, fallback ke mapel yang sudah ada nilainya
            if ($mapels->isEmpty()) {
                $mapelIds = Nilai::where('tahun_ajaran_id', $tahunAjaranId)
                    ->whereIn('siswa_id', $kelas->siswa()->pluck('id'))
                    ->distinct()
                    ->pluck('mata_pelajaran_id');
                $mapels = MataPelajaran::whereIn('id', $mapelIds)->orderBy('nama_mapel')->get();
            }

            // Ambil semua nilai siswa ini
            $nilaiSiswa = Nilai::where('siswa_id', $siswaId)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->with(['mataPelajaran', 'guru'])
                ->get()
                ->keyBy('mata_pelajaran_id');

            $waliKelas = $user;

            return view('wali_kelas.rapor.cetak', compact(
                'siswa', 'kelas', 'tahunAjaran', 'mapels', 'nilaiSiswa', 'waliKelas'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mencetak rapor: ' . $e->getMessage());
        }
    }

    /**
     * Cetak rapor 1 kelas — semua siswa, masing-masing 1 halaman
     */
    public function cetakKelas(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
        ], [
            'kelas_id.required' => 'Kelas harus dipilih',
            'tahun_ajaran_id.required' => 'Tahun ajaran harus dipilih',
        ]);

        $kelas = Kelas::with(['jurusan', 'waliKelas', 'siswa' => function ($q) {
            $q->orderBy('nama_siswa');
        }])->findOrFail($validated['kelas_id']);

        if (!$user->kelasAsWaliKelas()->whereId($kelas->id)->exists()) {
            abort(403, 'Anda bukan wali kelas untuk kelas ini');
        }

        $tahunAjaran = TahunAjaran::findOrFail($validated['tahun_ajaran_id']);

        // Ambil mapel dari pivot
        $mapels = $kelas->mataPelajarans()->orderBy('nama_mapel')->get();

        // Fallback jika pivot kosong
        if ($mapels->isEmpty()) {
            $mapelIds = Nilai::where('tahun_ajaran_id', $tahunAjaran->id)
                ->whereIn('siswa_id', $kelas->siswa->pluck('id'))
                ->distinct()
                ->pluck('mata_pelajaran_id');
            $mapels = MataPelajaran::whereIn('id', $mapelIds)->orderBy('nama_mapel')->get();
        }

        // Ambil semua nilai untuk kelas ini
        $allNilai = Nilai::where('tahun_ajaran_id', $tahunAjaran->id)
            ->whereIn('siswa_id', $kelas->siswa->pluck('id'))
            ->with(['mataPelajaran', 'guru'])
            ->get()
            ->groupBy('siswa_id');

        $siswaList = $kelas->siswa;
        $waliKelas = $user;

        return view('wali_kelas.rapor.cetak_kelas', compact(
            'kelas', 'tahunAjaran', 'mapels', 'siswaList', 'allNilai', 'waliKelas'
        ));
    }

    /**
     * Show statistik kelas - summary nilai siswa
     */
    public function statistikKelas(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('wali_kelas')) {
            abort(403, 'Unauthorized');
        }

        $kelasList = $user->kelasAsWaliKelas()->get();
        $tahunAjarans = TahunAjaran::where('is_active', true)->get();

        // Jika belum pilih filter, tampilkan form filter
        if (!$request->filled('kelas_id') || !$request->filled('tahun_ajaran_id')) {
            return view('wali_kelas.statistik', compact('kelasList', 'tahunAjarans'));
        }

        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
        ]);

        // Check if user is wali kelas for this kelas
        if (!$user->kelasAsWaliKelas()->whereId($validated['kelas_id'])->exists()) {
            abort(403, 'Anda bukan wali kelas untuk kelas ini');
        }

        $siswaNilai = $this->nilaiService->getNilaiByKelasAndTahun(
            $validated['kelas_id'],
            $validated['tahun_ajaran_id']
        );

        // Calculate statistics menggunakan RaporService
        $stats = [
            'total_siswa' => $siswaNilai->count(),
            'rata_rata_kelas' => 0,
            'siswa_lulus' => 0,
            'siswa_tidak_lulus' => 0,
        ];

        $totalNilai = 0;
        $totalSiswaWithNilai = 0;

        foreach ($siswaNilai as $siswa) {
            $nilaiSiswa = $siswa->nilai;
            if ($nilaiSiswa->count() > 0) {
                $rataRataSiswa = $nilaiSiswa->avg('nilai_angka');
                // Kelulusan: rata-rata >= 70
                if ($rataRataSiswa >= 70) {
                    $stats['siswa_lulus']++;
                } else {
                    $stats['siswa_tidak_lulus']++;
                }
                $totalNilai += $rataRataSiswa;
                $totalSiswaWithNilai++;
            }
        }

        if ($totalSiswaWithNilai > 0) {
            $stats['rata_rata_kelas'] = round($totalNilai / $totalSiswaWithNilai, 2);
        }

        $kelas = Kelas::with('jurusan')->find($validated['kelas_id']);
        $tahunAjaran = TahunAjaran::find($validated['tahun_ajaran_id']);

        return view('wali_kelas.statistik', compact('stats', 'kelas', 'tahunAjaran', 'siswaNilai', 'kelasList', 'tahunAjarans'));
    }
}
