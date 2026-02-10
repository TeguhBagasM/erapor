@extends('layouts.app')
@section('title', isset($guruData) ? 'Edit Guru' : 'Tambah Guru')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.guru.index') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali ke Data Guru
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                {{ isset($guruData) ? 'Edit Guru' : 'Tambah Guru Baru' }}
            </div>
            <div class="card-body">
                <form action="{{ isset($guruData) ? route('admin.guru.update', $guruData) : route('admin.guru.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($guruData)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control form-control-sm @error('nip') is-invalid @enderror"
                               value="{{ old('nip', $guruData->nip ?? '') }}" placeholder="Nomor Induk Pegawai">
                        @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Nama Guru <span class="text-danger">*</span></label>
                        <input type="text" name="nama_guru" class="form-control form-control-sm @error('nama_guru') is-invalid @enderror"
                               value="{{ old('nama_guru', $guruData->nama_guru ?? '') }}" placeholder="Nama lengkap">
                        @error('nama_guru') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Akun User (Opsional)</label>
                        <select name="user_id" class="form-select form-select-sm @error('user_id') is-invalid @enderror">
                            <option value="">— Tanpa Akun —</option>
                            @foreach($users ?? [] as $u)
                                <option value="{{ $u->id }}"
                                    {{ old('user_id', $guruData->user_id ?? '') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save me-1"></i>{{ isset($guruData) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('admin.guru.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
