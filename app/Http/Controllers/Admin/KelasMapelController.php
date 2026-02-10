<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Guru;
use Illuminate\Http\Request;

class KelasMapelController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    /**
     * Tampilkan daftar kelas dengan jumlah mapel yang terdaftar
     */
    public function index()
    {
        $kelas = Kelas::with(['jurusan', 'waliKelas', 'mataPelajarans'])->paginate(10);
        return view('admin.kelas-mapel.index', compact('kelas'));
    }

    /**
     * Form untuk mengatur mapel & guru pengajar di sebuah kelas
     */
    public function edit(Kelas $kelas)
    {
        $kelas->load(['jurusan', 'waliKelas', 'mataPelajarans']);
        $allMapel = MataPelajaran::orderBy('nama_mapel')->get();
        $allGuru = Guru::orderBy('nama_guru')->get();

        // Map existing assignments: mapel_id => guru_id
        $assigned = $kelas->mataPelajarans->pluck('pivot.guru_id', 'id')->toArray();

        return view('admin.kelas-mapel.edit', compact('kelas', 'allMapel', 'allGuru', 'assigned'));
    }

    /**
     * Simpan mapping mapel-guru untuk kelas
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'mapel' => ['nullable', 'array'],
            'mapel.*' => ['exists:mata_pelajarans,id'],
            'guru' => ['nullable', 'array'],
        ]);

        // Build sync data: [mapel_id => ['guru_id' => ...]]
        $syncData = [];
        $mapelIds = $validated['mapel'] ?? [];
        $guruMapping = $validated['guru'] ?? [];

        foreach ($mapelIds as $mapelId) {
            $guruId = $guruMapping[$mapelId] ?? null;
            $syncData[$mapelId] = ['guru_id' => $guruId ?: null];
        }

        $kelas->mataPelajarans()->sync($syncData);

        return redirect()->route('admin.kelas-mapel.index')
            ->with('success', "Mata pelajaran kelas {$kelas->nama_kelas} berhasil diperbarui (" . count($syncData) . " mapel)");
    }
}
