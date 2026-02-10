@extends('layouts.app')
@section('title', isset($jurusan) ? 'Edit Jurusan' : 'Tambah Jurusan')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.jurusans.index') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali ke Data Jurusan
    </a>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                {{ isset($jurusan) ? 'Edit Jurusan' : 'Tambah Jurusan Baru' }}
            </div>
            <div class="card-body">
                <form action="{{ isset($jurusan) ? route('admin.jurusans.update', $jurusan) : route('admin.jurusans.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($jurusan)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Nama Jurusan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_jurusan" class="form-control form-control-sm @error('nama_jurusan') is-invalid @enderror"
                               value="{{ old('nama_jurusan', $jurusan->nama_jurusan ?? '') }}" placeholder="Contoh: IPA, IPS, Teknik Komputer">
                        @error('nama_jurusan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save me-1"></i>{{ isset($jurusan) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('admin.jurusans.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
