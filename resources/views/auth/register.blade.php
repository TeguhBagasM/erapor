@extends('layouts.app')
@section('title', 'Daftar')

@section('content')
<div class="container">
    <div class="row justify-content-center" style="min-height:70vh;align-items:center;">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <i class="fas fa-graduation-cap" style="font-size:2.2rem;color:#4e73df;"></i>
                <h5 class="mt-2 mb-1">e-Rapor</h5>
                <p class="text-muted" style="font-size:.82rem;">Buat akun baru</p>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label" style="font-size:.85rem;font-weight:600;">Nama</label>
                            <input id="name" type="text" name="name"
                                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required autocomplete="name" autofocus
                                   placeholder="Nama lengkap">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label" style="font-size:.85rem;font-weight:600;">Email</label>
                            <input id="email" type="email" name="email"
                                   class="form-control form-control-sm @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required autocomplete="email"
                                   placeholder="email@contoh.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label" style="font-size:.85rem;font-weight:600;">Password</label>
                            <input id="password" type="password" name="password"
                                   class="form-control form-control-sm @error('password') is-invalid @enderror"
                                   required autocomplete="new-password" placeholder="Minimal 8 karakter">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password-confirm" class="form-label" style="font-size:.85rem;font-weight:600;">Konfirmasi Password</label>
                            <input id="password-confirm" type="password" name="password_confirmation"
                                   class="form-control form-control-sm"
                                   required autocomplete="new-password" placeholder="Ulangi password">
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-3">
                            <i class="fas fa-user-plus me-1"></i>Daftar
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center mt-3 text-muted" style="font-size:.82rem;">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
