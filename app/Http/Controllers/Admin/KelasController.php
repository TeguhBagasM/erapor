<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;

class KelasController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function index()
    {
        $kelas = Kelas::with('jurusan', 'waliKelas')->paginate(10);
        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        $jurusans = Jurusan::all();
        $waliKelas = User::whereHas('role', function ($query) {
            $query->where('name', 'wali_kelas');
        })->get();
        return view('admin.kelas.create', compact('jurusans', 'waliKelas'));
    }

    public function store(StoreKelasRequest $request)
    {
        try {
            Kelas::create($request->validated());
            return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan kelas: ' . $e->getMessage());
        }
    }

    public function show(Kelas $kelas)
    {
        $kelas->load('jurusan', 'waliKelas', 'siswa');
        return view('admin.kelas.show', compact('kelas'));
    }

    public function edit(Kelas $kelas)
    {
        $jurusans = Jurusan::all();
        $waliKelas = User::whereHas('role', function ($query) {
            $query->where('name', 'wali_kelas');
        })->get();
        return view('admin.kelas.edit', compact('kelas', 'jurusans', 'waliKelas'));
    }

    public function update(UpdateKelasRequest $request, Kelas $kelas)
    {
        try {
            $kelas->update($request->validated());
            return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah kelas: ' . $e->getMessage());
        }
    }

    public function destroy(Kelas $kelas)
    {
        try {
            $kelas->delete();
            return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kelas: ' . $e->getMessage());
        }
    }
}
