@extends('layouts.app')
@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="mb-3">
    <h1 class="page-title">Dashboard Wali Kelas</h1>
    <p class="text-muted mb-0" style="font-size:.82rem;">Selamat datang, {{ auth()->user()->name }}</p>
</div>

{{-- Kelas yang Diampu --}}
<div class="row g-3 mb-4">
    @forelse($kelas as $k)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width:42px;height:42px;background:#e8eaf6;">
                            <i class="fas fa-chalkboard text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $k->nama_kelas }}</h6>
                            <small class="text-muted">{{ $k->jurusan->nama_jurusan ?? '-' }}</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted" style="font-size:.82rem;">Jumlah Siswa</span>
                        <span class="fw-bold">{{ $k->siswa->count() }}</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('wali_kelas.nilai.index') }}" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="fas fa-pen-alt me-1"></i>Input Nilai
                        </a>
                        <a href="{{ route('wali_kelas.rapor.list', ['tahun_ajaran_id' => $tahunAjarans->where('is_active', true)->first()?->id]) }}"
                           class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="fas fa-file-alt me-1"></i>Rapor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox text-muted mb-3" style="font-size:2rem;"></i>
                    <p class="text-muted mb-0">Anda belum ditugaskan sebagai wali kelas</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

{{-- Tahun Ajaran Aktif --}}
@php $aktif = $tahunAjarans->where('is_active', true)->first(); @endphp
@if($aktif)
    <div class="card">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-calendar-alt text-muted me-2"></i>
                <span style="font-size:.85rem;">
                    <strong>Tahun Ajaran Aktif:</strong>
                    {{ $aktif->tahun_ajaran }} — {{ ucfirst($aktif->semester) }}
                </span>
            </div>
        </div>
    </div>
@endif

{{-- Quick Actions --}}
<div class="card mt-3">
    <div class="card-header">
        <i class="fas fa-bolt me-2 text-muted"></i>Aksi Cepat
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-auto">
                <a href="{{ route('wali_kelas.nilai.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-pen-alt me-1"></i>Input Nilai
                </a>
            </div>
            <div class="col-auto">
                <a href="{{ route('wali_kelas.statistik', ['kelas_id' => $kelas->first()?->id, 'tahun_ajaran_id' => $aktif?->id]) }}"
                   class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-chart-bar me-1"></i>Statistik Kelas
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
