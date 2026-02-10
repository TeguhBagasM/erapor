<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTahunAjaranRequest;
use App\Http\Requests\UpdateTahunAjaranRequest;
use App\Models\TahunAjaran;

class TahunAjaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function index()
    {
        $tahunAjarans = TahunAjaran::paginate(10);
        return view('admin.tahun-ajaran.index', compact('tahunAjarans'));
    }

    public function create()
    {
        return view('admin.tahun-ajaran.create');
    }

    public function store(StoreTahunAjaranRequest $request)
    {
        try {
            TahunAjaran::create($request->validated());
            return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan tahun ajaran: ' . $e->getMessage());
        }
    }

    public function show(TahunAjaran $tahunAjaran)
    {
        return view('admin.tahun-ajaran.show', compact('tahunAjaran'));
    }

    public function edit(TahunAjaran $tahunAjaran)
    {
        return view('admin.tahun-ajaran.edit', compact('tahunAjaran'));
    }

    public function update(UpdateTahunAjaranRequest $request, TahunAjaran $tahunAjaran)
    {
        try {
            $tahunAjaran->update($request->validated());
            return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah tahun ajaran: ' . $e->getMessage());
        }
    }

    public function destroy(TahunAjaran $tahunAjaran)
    {
        try {
            $tahunAjaran->delete();
            return redirect()->route('admin.tahun-ajaran.index')->with('success', 'Tahun ajaran berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus tahun ajaran: ' . $e->getMessage());
        }
    }
}
