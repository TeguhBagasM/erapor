@extends('layouts.app')
@section('title', isset($kelasData) ? 'Edit Kelas' : 'Tambah Kelas')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.kelas.index') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali ke Data Kelas
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                {{ isset($kelasData) ? 'Edit Kelas' : 'Tambah Kelas Baru' }}
            </div>
            <div class="card-body">
                <form action="{{ isset($kelasData) ? route('admin.kelas.update', $kelasData) : route('admin.kelas.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($kelasData)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kelas" class="form-control form-control-sm @error('nama_kelas') is-invalid @enderror"
                               value="{{ old('nama_kelas', $kelasData->nama_kelas ?? '') }}" placeholder="Contoh: X IPA 1">
                        @error('nama_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Jurusan <span class="text-danger">*</span></label>
                        <select name="jurusan_id" class="form-select form-select-sm @error('jurusan_id') is-invalid @enderror">
                            <option value="">— Pilih Jurusan —</option>
                            @foreach($jurusans ?? [] as $j)
                                <option value="{{ $j->id }}"
                                    {{ old('jurusan_id', $kelasData->jurusan_id ?? '') == $j->id ? 'selected' : '' }}>
                                    {{ $j->nama_jurusan }}
                                </option>
                            @endforeach
                        </select>
                        @error('jurusan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Wali Kelas</label>
                        <select name="wali_kelas_id" class="form-select form-select-sm @error('wali_kelas_id') is-invalid @enderror">
                            <option value="">— Pilih Wali Kelas —</option>
                            @foreach($users ?? [] as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('wali_kelas_id', $kelasData->wali_kelas_id ?? '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('wali_kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save me-1"></i>{{ isset($kelasData) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
