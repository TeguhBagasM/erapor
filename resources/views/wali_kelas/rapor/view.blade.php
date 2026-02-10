@extends('layouts.app')
@section('title', 'Rapor Siswa')

@section('content')
<div class="mb-3">
    <a href="javascript:history.back()" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Rapor Semester</h1>
        <p class="text-muted mb-0" style="font-size:.82rem;">
            {{ $data['siswa']->nama_siswa ?? '' }} &bull;
            {{ $data['siswa']->kelas->nama_kelas ?? '' }} &bull;
            {{ $data['tahun_ajaran']->tahun_ajaran ?? '' }} {{ ucfirst($data['tahun_ajaran']->semester ?? '') }}
        </p>
    </div>
    @if(isset($data['siswa']) && isset($data['tahun_ajaran']))
        <div class="d-flex gap-2">
            <a href="{{ route('wali_kelas.rapor.download', [$data['siswa']->id, $data['tahun_ajaran']->id]) }}"
               class="btn btn-sm btn-outline-success" target="_blank">
                <i class="fas fa-print me-1"></i>Cetak Rapor
            </a>
            <a href="{{ route('wali_kelas.rapor.download', [$data['siswa']->id, $data['tahun_ajaran']->id]) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="fas fa-download me-1"></i>Download
            </a>
        </div>
    @endif
</div>

{{-- Info Siswa --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted d-block" style="font-size:.78rem;">NIS</small>
                <span style="font-size:.85rem;"><code>{{ $data['siswa']->nis ?? '-' }}</code></span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block" style="font-size:.78rem;">Nama Siswa</small>
                <span style="font-size:.85rem;">{{ $data['siswa']->nama_siswa ?? '-' }}</span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block" style="font-size:.78rem;">Kelas</small>
                <span style="font-size:.85rem;">{{ $data['siswa']->kelas->nama_kelas ?? '-' }}</span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block" style="font-size:.78rem;">Tahun Ajaran</small>
                <span style="font-size:.85rem;">
                    {{ $data['tahun_ajaran']->tahun_ajaran ?? '-' }} — {{ ucfirst($data['tahun_ajaran']->semester ?? '-') }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Nilai --}}
<div class="card mb-3">
    <div class="card-header">
        <i class="fas fa-table me-2 text-muted"></i>Daftar Nilai
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengajar</th>
                        <th class="text-center" style="width:110px">Nilai Angka</th>
                        <th class="text-center" style="width:100px">Nilai Huruf</th>
                        <th class="text-center" style="width:100px">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['nilai'] ?? [] as $i => $n)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>{{ $n->mataPelajaran->nama_mapel ?? '-' }}</td>
                            <td class="text-muted" style="font-size:.82rem;">{{ $n->guru->nama_guru ?? '-' }}</td>
                            <td class="text-center fw-bold">{{ $n->nilai_angka }}</td>
                            <td class="text-center">
                                @php
                                    $badgeClass = match($n->nilai_huruf) {
                                        'A' => 'bg-success',
                                        'B' => 'bg-primary',
                                        'C' => 'bg-warning',
                                        'D' => 'bg-secondary',
                                        'E' => 'bg-danger',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $n->nilai_huruf }}</span>
                            </td>
                            <td class="text-center text-muted" style="font-size:.82rem;">
                                @php
                                    echo match($n->nilai_huruf) {
                                        'A' => 'Sangat Baik',
                                        'B' => 'Baik',
                                        'C' => 'Cukup',
                                        'D' => 'Kurang',
                                        'E' => 'Sangat Kurang',
                                        default => '-'
                                    };
                                @endphp
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data nilai</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(isset($data['rata_rata']))
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="3" class="fw-bold">Rata-rata</td>
                            <td class="text-center fw-bold">{{ number_format($data['rata_rata'], 1) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- Ringkasan --}}
@if(isset($data['nilai']) && count($data['nilai']) > 0)
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center py-3">
                    <small class="text-muted d-block" style="font-size:.78rem;">Jumlah Mapel</small>
                    <span class="fw-bold" style="font-size:1.3rem;">{{ count($data['nilai']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center py-3">
                    <small class="text-muted d-block" style="font-size:.78rem;">Rata-rata</small>
                    <span class="fw-bold" style="font-size:1.3rem;">{{ number_format($data['rata_rata'] ?? 0, 1) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center py-3">
                    <small class="text-muted d-block" style="font-size:.78rem;">Status</small>
                    @php $rataRata = $data['rata_rata'] ?? 0; @endphp
                    <span class="badge bg-{{ $rataRata >= 70 ? 'success' : 'danger' }}" style="font-size:.85rem;">
                        {{ $rataRata >= 70 ? 'LULUS' : 'BELUM LULUS' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
