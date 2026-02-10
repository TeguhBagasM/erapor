<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGridNilaiRequest;
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
     * index() - Grid input nilai (pilih kelas + tahun ajaran, tampil semua siswa × mapel)
     */
    public function index()
    {
        $user = Auth::user();
        $kelas = $user->kelasAsWaliKelas()->get();
        $tahunAjarans = TahunAjaran::all();

        return view('wali_kelas.nilai.index', compact('kelas', 'tahunAjarans'));
    }

    /**
     * storeGrid() - Simpan seluruh nilai grid via AJAX
     */
    public function storeGrid(StoreGridNilaiRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Pastikan wali kelas hanya bisa input ke kelasnya sendiri
        $kelasExists = $user->kelasAsWaliKelas()
            ->where('id', $validated['kelas_id'])
            ->exists();

        if (!$kelasExists) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke kelas ini',
            ], 403);
        }

        try {
            $result = $this->nilaiService->storeGridNilai($validated);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => "Nilai berhasil disimpan. {$result['successCount']} data berhasil, {$result['failedCount']} gagal",
                    'data' => $result,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Gagal menyimpan nilai',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
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
}
