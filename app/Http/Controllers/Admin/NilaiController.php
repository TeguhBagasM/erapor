<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBulkNilaiRequest;
use App\Http\Requests\ImportExcelRequest;
use App\Services\NilaiService;
use App\Services\ImportService;
use App\Services\RaporService;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use App\Models\Guru;
use App\Models\Siswa;
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
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        $tahunAjarans = TahunAjaran::all();
        return view('admin.nilai.import', compact('tahunAjarans'));
    }

    /**
     * Process import Excel
     */
    public function import(ImportExcelRequest $request)
    {
        try {
            $importLog = $this->importService->processImport(
                $request->file('file'),
                $request->tipe_import,
                $request->tahun_ajaran_id
            );

            return redirect()->back()->with('success', "Import berhasil! {$importLog->success_rows} data berhasil diproses.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal melakukan import: ' . $e->getMessage())
                ->withInput();
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
            $data = $this->raporService->exportRaporData($siswaId, $tahunAjaranId);

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
        $data = $this->raporService->exportRaporData($siswaId, $tahunAjaranId);
        return view('admin.rapor.view', compact('data'));
    }
}
