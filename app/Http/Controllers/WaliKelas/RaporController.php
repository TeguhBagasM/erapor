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

        return view('wali_kelas.rapor.list', compact('siswa', 'tahunAjaran'));
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

        $data = $this->raporService->exportRaporData($siswaId, $tahunAjaranId);
        return view('wali_kelas.rapor.view', compact('data'));
    }

    /**
     * Download rapor PDF
     */
    public function downloadRapor($siswaId, $tahunAjaranId)
    {
        $user = Auth::user();
        $siswa = Siswa::findOrFail($siswaId);

        // Check authorization
        if (!$user->kelasAsWaliKelas()->whereId($siswa->kelas_id)->exists()) {
            abort(403, 'Unauthorized');
        }

        try {
            $data = $this->raporService->exportRaporData($siswaId, $tahunAjaranId);

            // TODO: Implementasikan PDF generation
            // Untuk sekarang return JSON
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
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

        // Calculate statistics
        $stats = [
            'total_siswa' => $siswaNilai->count(),
            'rata_rata_kelas' => 0,
            'siswa_lulus' => 0,
            'siswa_tidak_lulus' => 0,
        ];

        $totalNilai = 0;
        $totalMataPelajaran = 0;

        foreach ($siswaNilai as $siswa) {
            $nilaiSiswa = $siswa->nilai;
            if ($nilaiSiswa->count() > 0) {
                $rataRataSiswa = $nilaiSiswa->avg('nilai_angka');
                if ($this->raporService->isLulus($rataRataSiswa)) {
                    $stats['siswa_lulus']++;
                } else {
                    $stats['siswa_tidak_lulus']++;
                }
                $totalNilai += $rataRataSiswa;
                $totalMataPelajaran++;
            }
        }

        if ($totalMataPelajaran > 0) {
            $stats['rata_rata_kelas'] = round($totalNilai / $totalMataPelajaran, 2);
        }

        $kelas = Kelas::with('jurusan')->find($validated['kelas_id']);
        $tahunAjaran = TahunAjaran::find($validated['tahun_ajaran_id']);

        return view('wali_kelas.statistik', compact('stats', 'kelas', 'tahunAjaran', 'siswaNilai'));
    }
}
