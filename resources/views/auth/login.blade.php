@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center" style="min-height:70vh;align-items:center;">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <i class="fas fa-graduation-cap" style="font-size:2.2rem;color:#4e73df;"></i>
                <h5 class="mt-2 mb-1">e-Rapor</h5>
                <p class="text-muted" style="font-size:.82rem;">Masuk ke akun Anda</p>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label" style="font-size:.85rem;font-weight:600;">Email</label>
                            <input id="email" type="email" name="email"
                                   class="form-control form-control-sm @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required autocomplete="email" autofocus
                                   placeholder="email@contoh.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label" style="font-size:.85rem;font-weight:600;">Password</label>
                            <input id="password" type="password" name="password"
                                   class="form-control form-control-sm @error('password') is-invalid @enderror"
                                   required autocomplete="current-password" placeholder="••••••••">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember" style="font-size:.82rem;">
                                    Ingat Saya
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-1"></i>Masuk
                        </button>

                        @if (Route::has('password.request'))
                            <div class="text-center">
                                <a href="{{ route('password.request') }}" style="font-size:.82rem;">Lupa Password?</a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            @if (Route::has('register'))
                <p class="text-center mt-3 text-muted" style="font-size:.82rem;">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
                </p>
            @endif
        </div>
    </div>
</div>
@endsection
