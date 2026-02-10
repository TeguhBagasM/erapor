@extends('layouts.app')
@section('title', 'Statistik Kelas')

@section('content')
<div class="mb-3">
    <a href="{{ route('wali_kelas.dashboard') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
    </a>
</div>

<div class="mb-3">
    <h1 class="page-title">Statistik Kelas</h1>
    <p class="text-muted mb-0" style="font-size:.82rem;">
        {{ $kelas->nama_kelas }} ({{ $kelas->jurusan->nama_jurusan ?? '-' }}) &bull;
        {{ $tahunAjaran->tahun_ajaran }} Smt {{ $tahunAjaran->semester }}
    </p>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body text-center py-3">
                <small class="text-muted d-block" style="font-size:.78rem;">Total Siswa</small>
                <span class="fw-bold" style="font-size:1.5rem;">{{ $stats['total_siswa'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body text-center py-3">
                <small class="text-muted d-block" style="font-size:.78rem;">Rata-rata Kelas</small>
                <span class="fw-bold" style="font-size:1.5rem;">{{ $stats['rata_rata_kelas'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body text-center py-3">
                <small class="text-muted d-block" style="font-size:.78rem;">Siswa Lulus</small>
                <span class="fw-bold text-success" style="font-size:1.5rem;">{{ $stats['siswa_lulus'] }}</span>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card">
            <div class="card-body text-center py-3">
                <small class="text-muted d-block" style="font-size:.78rem;">Tidak Lulus</small>
                <span class="fw-bold text-danger" style="font-size:1.5rem;">{{ $stats['siswa_tidak_lulus'] }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Progress Bar --}}
@if($stats['total_siswa'] > 0)
    @php
        $pctLulus = round(($stats['siswa_lulus'] / $stats['total_siswa']) * 100);
    @endphp
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <small class="fw-bold" style="font-size:.82rem;">Tingkat Kelulusan</small>
                <small class="text-muted" style="font-size:.82rem;">{{ $pctLulus }}%</small>
            </div>
            <div class="progress" style="height:8px;">
                <div class="progress-bar bg-success" style="width:{{ $pctLulus }}%"></div>
            </div>
        </div>
    </div>
@endif

{{-- Tabel Detail Siswa --}}
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2 text-muted"></i>Detail Nilai per Siswa
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th class="text-center" style="width:100px">Jml Nilai</th>
                        <th class="text-center" style="width:110px">Rata-rata</th>
                        <th class="text-center" style="width:100px">Status</th>
                        <th style="width:80px" class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswaNilai as $i => $s)
                        @php
                            $avgNilai = $s->nilai->count() > 0 ? round($s->nilai->avg('nilai_angka'), 1) : 0;
                            $lulus = $avgNilai >= 70;
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td><code>{{ $s->nis }}</code></td>
                            <td>{{ $s->nama_siswa }}</td>
                            <td class="text-center">{{ $s->nilai->count() }}</td>
                            <td class="text-center fw-bold">{{ $avgNilai }}</td>
                            <td class="text-center">
                                @if($s->nilai->count() > 0)
                                    <span class="badge bg-{{ $lulus ? 'success' : 'danger' }}">
                                        {{ $lulus ? 'Lulus' : 'Tidak Lulus' }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:.78rem;">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('wali_kelas.nilai.show', $s->id) }}"
                                   class="btn btn-sm btn-outline-secondary py-0 px-2" title="Detail">
                                    <i class="fas fa-eye" style="font-size:.7rem;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Tidak ada data siswa</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
