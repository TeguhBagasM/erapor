<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Models\Siswa;
use App\Models\Kelas;

class SiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function index()
    {
        $siswa = Siswa::with('kelas')->get();
        return view('admin.siswa.index', compact('siswa'));
    }

    public function create()
    {
        $kelas = Kelas::all();
        return view('admin.siswa.create', compact('kelas'));
    }

    public function store(StoreSiswaRequest $request)
    {
        try {
            Siswa::create($request->validated());
            return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan siswa: ' . $e->getMessage());
        }
    }

    public function show(Siswa $siswa)
    {
        $siswa->load('kelas');
        return view('admin.siswa.show', compact('siswa'));
    }

    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(UpdateSiswaRequest $request, Siswa $siswa)
    {
        try {
            $siswa->update($request->validated());
            return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah siswa: ' . $e->getMessage());
        }
    }

    public function destroy(Siswa $siswa)
    {
        try {
            $siswa->delete();
            return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus siswa: ' . $e->getMessage());
        }
    }
}
