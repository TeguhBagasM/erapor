@extends('layouts.app')
@section('title', 'Rekap Nilai — ' . $siswa->nama_siswa)

@section('content')
<div class="mb-3">
    <a href="{{ route('wali_kelas.nilai.index') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Rekap Nilai Siswa</h1>
        <p class="text-muted mb-0" style="font-size:.82rem;">
            {{ $siswa->nama_siswa }} &bull; NIS: {{ $siswa->nis }} &bull; {{ $siswa->kelas->nama_kelas ?? '-' }}
        </p>
    </div>
    @if($rekap && isset($rekap['tahun_ajaran']))
        <a href="{{ route('wali_kelas.rapor.view', [$siswa->id, $rekap['tahun_ajaran']->id ?? '']) }}"
           class="btn btn-sm btn-outline-primary">
            <i class="fas fa-file-alt me-1"></i>Lihat Rapor
        </a>
    @endif
</div>

{{-- Info Siswa --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row">
            <div class="col-md-4">
                <small class="text-muted d-block" style="font-size:.78rem;">NIS</small>
                <span style="font-size:.85rem;"><code>{{ $siswa->nis }}</code></span>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block" style="font-size:.78rem;">Nama</small>
                <span style="font-size:.85rem;">{{ $siswa->nama_siswa }}</span>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block" style="font-size:.78rem;">Kelas</small>
                <span style="font-size:.85rem;">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Rekap Nilai --}}
<div class="card">
    <div class="card-header">
        <i class="fas fa-table me-2 text-muted"></i>Rekap Nilai
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Mata Pelajaran</th>
                        <th class="text-center" style="width:120px">Nilai Angka</th>
                        <th class="text-center" style="width:100px">Nilai Huruf</th>
                        <th class="text-center" style="width:100px">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    @if($rekap && isset($rekap['nilai']) && count($rekap['nilai']) > 0)
                        @foreach($rekap['nilai'] as $i => $n)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>{{ $n['mata_pelajaran'] ?? $n->mataPelajaran->nama_mapel ?? '-' }}</td>
                                <td class="text-center fw-bold">{{ $n['nilai_angka'] ?? $n->nilai_angka ?? '-' }}</td>
                                <td class="text-center">
                                    @php
                                        $huruf = $n['nilai_huruf'] ?? $n->nilai_huruf ?? '-';
                                        $badgeClass = match($huruf) {
                                            'A' => 'bg-success',
                                            'B' => 'bg-primary',
                                            'C' => 'bg-warning',
                                            'D' => 'bg-secondary',
                                            'E' => 'bg-danger',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $huruf }}</span>
                                </td>
                                <td class="text-center text-muted" style="font-size:.82rem;">
                                    {{ $n['predikat'] ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data nilai</td>
                        </tr>
                    @endif
                </tbody>
                @if($rekap && isset($rekap['rata_rata']))
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="2" class="fw-bold">Rata-rata</td>
                            <td class="text-center fw-bold">{{ number_format($rekap['rata_rata'], 1) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
