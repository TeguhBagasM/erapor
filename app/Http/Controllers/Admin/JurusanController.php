<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJurusanRequest;
use App\Http\Requests\UpdateJurusanRequest;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function index()
    {
        $jurusans = Jurusan::withCount('kelas')->get();
        return view('admin.jurusan.index', compact('jurusans'));
    }

    public function create()
    {
        return view('admin.jurusan.create');
    }

    public function store(StoreJurusanRequest $request)
    {
        try {
            Jurusan::create($request->validated());
            return redirect()->route('admin.jurusans.index')->with('success', 'Jurusan berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan jurusan: ' . $e->getMessage());
        }
    }

    public function show(Jurusan $jurusan)
    {
        return view('admin.jurusan.show', compact('jurusan'));
    }

    public function edit(Jurusan $jurusan)
    {
        return view('admin.jurusan.edit', compact('jurusan'));
    }

    public function update(UpdateJurusanRequest $request, Jurusan $jurusan)
    {
        try {
            $jurusan->update($request->validated());
            return redirect()->route('admin.jurusans.index')->with('success', 'Jurusan berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah jurusan: ' . $e->getMessage());
        }
    }

    public function destroy(Jurusan $jurusan)
    {
        try {
            $jurusan->delete();
            return redirect()->route('admin.jurusans.index')->with('success', 'Jurusan berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus jurusan: ' . $e->getMessage());
        }
    }
}
