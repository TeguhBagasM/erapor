@extends('layouts.app')
@section('title', isset($siswa) ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.siswa.index') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali ke Data Siswa
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                {{ isset($siswa) ? 'Edit Siswa' : 'Tambah Siswa Baru' }}
            </div>
            <div class="card-body">
                <form action="{{ isset($siswa) ? route('admin.siswa.update', $siswa) : route('admin.siswa.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($siswa)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control form-control-sm @error('nis') is-invalid @enderror"
                               value="{{ old('nis', $siswa->nis ?? '') }}" placeholder="Nomor Induk Siswa">
                        @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Nama Siswa <span class="text-danger">*</span></label>
                        <input type="text" name="nama_siswa" class="form-control form-control-sm @error('nama_siswa') is-invalid @enderror"
                               value="{{ old('nama_siswa', $siswa->nama_siswa ?? '') }}" placeholder="Nama lengkap">
                        @error('nama_siswa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" class="form-select form-select-sm @error('kelas_id') is-invalid @enderror">
                            <option value="">— Pilih Kelas —</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}"
                                    {{ old('kelas_id', $siswa->kelas_id ?? '') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save me-1"></i>{{ isset($siswa) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('admin.siswa.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
