<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Models\Guru;
use App\Models\User;

class GuruController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }

    public function index()
    {
        $guru = Guru::with('user')->paginate(10);
        return view('admin.guru.index', compact('guru'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.guru.create', compact('users'));
    }

    public function store(StoreGuruRequest $request)
    {
        try {
            Guru::create($request->validated());
            return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan guru: ' . $e->getMessage());
        }
    }

    public function show(Guru $guru)
    {
        $guru->load('user');
        return view('admin.guru.show', compact('guru'));
    }

    public function edit(Guru $guru)
    {
        $users = User::all();
        return view('admin.guru.edit', compact('guru', 'users'));
    }

    public function update(UpdateGuruRequest $request, Guru $guru)
    {
        try {
            $guru->update($request->validated());
            return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil diubah');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah guru: ' . $e->getMessage());
        }
    }

    public function destroy(Guru $guru)
    {
        try {
            $guru->delete();
            return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus guru: ' . $e->getMessage());
        }
    }
}
