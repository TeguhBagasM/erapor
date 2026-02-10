@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="text-muted mb-0" style="font-size:.82rem;">Selamat datang, {{ Auth::user()->name }}</p>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <div class="stat-value">{{ \App\Models\Siswa::count() }}</div>
                    <div class="stat-label">Siswa</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <div class="stat-value">{{ \App\Models\Guru::count() }}</div>
                    <div class="stat-label">Guru</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="fas fa-door-open"></i>
                </div>
                <div>
                    <div class="stat-value">{{ \App\Models\Kelas::count() }}</div>
                    <div class="stat-label">Kelas</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <div class="stat-value">{{ \App\Models\MataPelajaran::count() }}</div>
                    <div class="stat-label">Mata Pelajaran</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick actions --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt me-2 text-muted"></i>Aksi Cepat
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.siswa.create') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-plus me-1"></i>Tambah Siswa
                    </a>
                    <a href="{{ route('admin.guru.create') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-plus me-1"></i>Tambah Guru
                    </a>
                    <a href="{{ route('admin.nilai.create') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-pen me-1"></i>Input Nilai
                    </a>
                    <a href="{{ route('admin.import.form') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-file-import me-1"></i>Import Data
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-alt me-2 text-muted"></i>Tahun Ajaran Aktif
            </div>
            <div class="card-body">
                @php $tahunAktif = \App\Models\TahunAjaran::where('is_active', true)->first(); @endphp
                @if($tahunAktif)
                    <h5 class="mb-1">{{ $tahunAktif->tahun_ajaran }}</h5>
                    <span class="badge bg-success">Semester {{ $tahunAktif->semester }}</span>
                @else
                    <p class="text-muted mb-0">Belum ada tahun ajaran aktif</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
        </div>
    </div>
</div>
@endsection
