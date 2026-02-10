<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBulkNilaiRequest;
use App\Http\Requests\ImportExcelRequest;
use App\Services\NilaiService;
use App\Services\ImportService;
use App\Services\RaporService;
use App\Exports\SiswaTemplate;
use App\Exports\NilaiTemplate;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\Siswa;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class NilaiController extends Controller
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
        $this->middleware('isAdmin');
    }

    /**
     * Show form untuk input nilai massal
     */
    public function create()
    {
        $tahunAjarans = TahunAjaran::all();
        $mataPelajarans = MataPelajaran::all();
        $guru = Guru::with('user')->get();
        $kelas = Kelas::all();

        return view('admin.nilai.create', compact('tahunAjarans', 'mataPelajarans', 'guru', 'kelas'));
    }

    /**
     * Store bulk nilai dari form
     */
    public function store(StoreBulkNilaiRequest $request)
    {
        try {
            // Validasi rentang nilai
            foreach ($request->validated()['nilai'] as $nilaiData) {
                $this->raporService->validateNilaiRange($nilaiData['nilai_angka']);
            }

            $result = $this->nilaiService->storeBulkNilai($request->validated());

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
     * Show import form
     */
    public function showImportForm()
    {
        $tahunAjarans = TahunAjaran::all();
        return view('admin.import.index', compact('tahunAjarans'));
    }

    /**
     * Process import Excel
     */
    public function import(ImportExcelRequest $request)
    {
        try {
            $tipeImport = $request->tipe_import;

            if ($tipeImport === 'siswa') {
                $result = $this->importService->importSiswa($request->file('file'));
            } elseif ($tipeImport === 'nilai') {
                $guru = auth()->user()->guru;
                if (!$guru) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Admin belum terdaftar sebagai guru',
                    ], 400);
                }

                $result = $this->importService->importNilai(
                    $request->file('file'),
                    $guru->id,
                    $request->tahun_ajaran_id
                );
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipe import tidak dikenal',
                ], 400);
            }

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'success_count' => $result['success_count'],
                    'errors' => $result['errors'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'errors' => $result['errors'] ?? [],
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List nilai by kelas and tahun ajaran
     */
    public function listByKelas(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
        ]);

        $siswaNilai = $this->nilaiService->getNilaiByKelasAndTahun(
            $validated['kelas_id'],
            $validated['tahun_ajaran_id']
        );

        return view('admin.nilai.list', compact('siswaNilai'));
    }

    /**
     * Download rapor PDF
     */
    public function downloadRapor($siswaId, $tahunAjaranId)
    {
        try {
            $data = $this->raporService->generateRaporSemester($siswaId, $tahunAjaranId);

            // TODO: Implementasikan PDF generation dengan mPDF atau DOMPDF
            // Contoh dengan mPDF:
            // $pdf = new Mpdf\Mpdf();
            // $pdf->WriteHTML(view('rapor.template', $data)->render());
            // return $pdf->Output('rapor.pdf', 'D');

            // Untuk sekarang return data sebagai JSON
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * View rapor
     */
    public function viewRapor($siswaId, $tahunAjaranId)
    {
        $data = $this->raporService->generateRaporSemester($siswaId, $tahunAjaranId);
        return view('admin.rapor.view', compact('data'));
    }

    /**
     * Download template siswa
     */
    public function downloadTemplateSiswa()
    {
        return Excel::download(new SiswaTemplate(), 'template_siswa.xlsx');
    }

    /**
     * Download template nilai
     */
    public function downloadTemplateNilai()
    {
        return Excel::download(new NilaiTemplate(), 'template_nilai.xlsx');
    }
}
