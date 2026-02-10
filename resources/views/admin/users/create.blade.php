@extends('layouts.app')
@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.users.index') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali ke Data User
    </a>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                {{ isset($user) ? 'Edit User' : 'Tambah User Baru' }}
            </div>
            <div class="card-body">
                <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($user)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name ?? '') }}" placeholder="Nama lengkap">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email ?? '') }}" placeholder="email@contoh.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">
                            Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '' }}
                            @if(!isset($user)) <span class="text-danger">*</span> @endif
                        </label>
                        <input type="password" name="password" class="form-control form-control-sm @error('password') is-invalid @enderror"
                               placeholder="Minimal 8 karakter">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control form-control-sm"
                               placeholder="Ulangi password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Role <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select form-select-sm @error('role_id') is-invalid @enderror">
                            <option value="">— Pilih Role —</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ old('role_id', $user->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save me-1"></i>{{ isset($user) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
