<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMataPelajaranRequest;
use App\Http\Requests\UpdateMataPelajaranRequest;
use App\Models\MataPelajaran;

class MataPelajaranController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function index()
    {
        $mataPelajarans = MataPelajaran::paginate(10);
        return view('admin.mata-pelajaran.index', compact('mataPelajarans'));
    }

    public function create()
    {
        return view('admin.mata-pelajaran.create');
    }

    public function store(StoreMataPelajaranRequest $request)
    {
        try {
            MataPelajaran::create($request->validated());
            return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan mata pelajaran: ' . $e->getMessage());
        }
    }

    public function show(MataPelajaran $mataPelajaran)
    {
        return view('admin.mata-pelajaran.show', compact('mataPelajaran'));
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        return view('admin.mata-pelajaran.edit', compact('mataPelajaran'));
    }

    public function update(UpdateMataPelajaranRequest $request, MataPelajaran $mataPelajaran)
    {
        try {
            $mataPelajaran->update($request->validated());
            return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah mata pelajaran: ' . $e->getMessage());
        }
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        try {
            $mataPelajaran->delete();
            return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Mata pelajaran berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus mata pelajaran: ' . $e->getMessage());
        }
    }
}
