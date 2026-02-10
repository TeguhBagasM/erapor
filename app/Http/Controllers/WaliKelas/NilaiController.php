<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNilaiWaliKelasRequest;
use App\Services\NilaiService;
use App\Services\RaporService;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    private NilaiService $nilaiService;
    private RaporService $raporService;

    public function __construct(
        NilaiService $nilaiService,
        RaporService $raporService
    ) {
        $this->nilaiService = $nilaiService;
        $this->raporService = $raporService;
        $this->middleware('role:wali_kelas');
    }

    /**
     * index() - Form untuk pilih kelas, mata pelajaran, tahun ajaran
     */
    public function index()
    {
        $user = Auth::user();
        $kelas = $user->kelasAsWaliKelas()->get();

        // Get tahun ajaran aktif
        $tahunAjarans = TahunAjaran::where('is_active', true)->get();
        $mataPelajarans = MataPelajaran::all();

        return view('wali_kelas.nilai.index', compact('kelas', 'mataPelajarans', 'tahunAjarans'));
    }

    /**
     * create() - Tampilkan tabel siswa dengan input nilai bulk
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
        ]);

        // Pastikan wali kelas hanya bisa akses kelasnya sendiri
        $kelasExists = $user->kelasAsWaliKelas()
            ->where('id', $validated['kelas_id'])
            ->exists();

        if (!$kelasExists) {
            return redirect()->route('wali_kelas.nilai.index')
                ->with('error', 'Anda tidak memiliki akses ke kelas ini');
        }

        // Get kelas detail
        $kelas = Kelas::with('siswa')->find($validated['kelas_id']);
        $mataPelajaran = MataPelajaran::find($validated['mata_pelajaran_id']);
        $tahunAjaran = TahunAjaran::find($validated['tahun_ajaran_id']);

        // Get existing nilai untuk kelas & mata pelajaran ini
        $existingNilai = Nilai::where('mata_pelajaran_id', $validated['mata_pelajaran_id'])
            ->where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
            ->whereIn('siswa_id', $kelas->siswa->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        return view('wali_kelas.nilai.create', compact(
            'kelas',
            'mataPelajaran',
            'tahunAjaran',
            'existingNilai',
            'validated'
        ));
    }

    /**
     * store() - Simpan seluruh nilai dengan transaction
     */
    public function store(StoreNilaiWaliKelasRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Pastikan wali kelas hanya bisa input ke kelasnya sendiri
        $kelasExists = $user->kelasAsWaliKelas()
            ->where('id', $validated['kelas_id'])
            ->exists();

        if (!$kelasExists) {
            return back()->with('error', 'Anda tidak memiliki akses ke kelas ini');
        }

        try {
            DB::beginTransaction();

            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($validated['nilai'] as $index => $nilaiData) {
                try {
                    // Get guru melalui user yang sedang login
                    $guru = $user->guru;
                    if (!$guru) {
                        throw new \Exception('Anda belum terdaftar sebagai guru');
                    }

                    // Validasi rentang nilai menggunakan RaporService
                    $this->raporService->validateNilaiRange($nilaiData['nilai_angka']);

                    // Hitung nilai huruf otomatis menggunakan RaporService
                    $nilaiHuruf = $this->raporService->convertNilaiToHuruf($nilaiData['nilai_angka']);

                    // updateOrCreate - update jika sudah ada, create jika belum
                    Nilai::updateOrCreate(
                        [
                            'siswa_id' => $nilaiData['siswa_id'],
                            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
                            'guru_id' => $guru->id,
                            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                        ],
                        [
                            'nilai_angka' => $nilaiData['nilai_angka'],
                            'nilai_huruf' => $nilaiHuruf,
                        ]
                    );

                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'row' => $index + 1,
                        'siswa_id' => $nilaiData['siswa_id'] ?? null,
                        'message' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();

            $message = "Nilai berhasil disimpan! {$successCount} data berhasil";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} gagal";
            }

            return redirect()->route('wali_kelas.nilai.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * show() - Lihat detail nilai siswa tertentu dengan rekap lengkap
     */
    public function show($siswaId)
    {
        $user = Auth::user();
        $siswa = Siswa::findOrFail($siswaId);

        // Pastikan siswa ada di kelas wali kelas ini
        $kelasExists = $user->kelasAsWaliKelas()
            ->where('id', $siswa->kelas_id)
            ->exists();

        if (!$kelasExists) {
            return redirect()->route('wali_kelas.nilai.index')
                ->with('error', 'Anda tidak memiliki akses ke siswa ini');
        }

        // Get tahun ajaran aktif
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAjaran) {
            return redirect()->route('wali_kelas.nilai.index')
                ->with('error', 'Tidak ada tahun ajaran aktif');
        }

        // Get rekap nilai menggunakan RaporService
        $rekap = $this->raporService->rekapNilaiSiswa($siswaId, $tahunAjaran->id);

        return view('wali_kelas.nilai.show', compact('siswa', 'rekap'));
    }

    /**
     * exportRapor() - Generate data rapor semester untuk siswa
     */
    public function exportRapor($siswaId, $tahunAjaranId = null)
    {
        try {
            $user = Auth::user();
            $siswa = Siswa::findOrFail($siswaId);

            // Pastikan siswa ada di kelas wali kelas ini
            $kelasExists = $user->kelasAsWaliKelas()
                ->where('id', $siswa->kelas_id)
                ->exists();

            if (!$kelasExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke siswa ini',
                ], 403);
            }

            // Jika tidak ada tahun ajaran, gunakan tahun ajaran aktif
            if (!$tahunAjaranId) {
                $tahunAjaran = TahunAjaran::where('is_active', true)->firstOrFail();
                $tahunAjaranId = $tahunAjaran->id;
            }

            // Generate rapor menggunakan RaporService
            $raporData = $this->raporService->generateRaporSemester($siswaId, $tahunAjaranId);

            return response()->json([
                'success' => true,
                'data' => $raporData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
