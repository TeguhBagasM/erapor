@extends('layouts.app')
@section('title', isset($mataPelajaran) ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.mata-pelajaran.index') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali ke Data Mata Pelajaran
    </a>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                {{ isset($mataPelajaran) ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran Baru' }}
            </div>
            <div class="card-body">
                <form action="{{ isset($mataPelajaran) ? route('admin.mata-pelajaran.update', $mataPelajaran) : route('admin.mata-pelajaran.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($mataPelajaran)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Kode Mapel <span class="text-danger">*</span></label>
                        <input type="text" name="kode_mapel" class="form-control form-control-sm @error('kode_mapel') is-invalid @enderror"
                               value="{{ old('kode_mapel', $mataPelajaran->kode_mapel ?? '') }}" placeholder="Contoh: MTK101">
                        @error('kode_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" name="nama_mapel" class="form-control form-control-sm @error('nama_mapel') is-invalid @enderror"
                               value="{{ old('nama_mapel', $mataPelajaran->nama_mapel ?? '') }}" placeholder="Contoh: Matematika">
                        @error('nama_mapel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save me-1"></i>{{ isset($mataPelajaran) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
