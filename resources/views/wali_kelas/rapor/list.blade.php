@extends('layouts.app')
@section('title', 'Rekap Nilai')

@section('content')
<div class="mb-3">
    <h1 class="page-title">Rekap Nilai Siswa</h1>
    <p class="text-muted mb-0" style="font-size:.82rem;">Pilih tahun ajaran untuk melihat rekap nilai</p>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-2 text-muted"></i>Filter
    </div>
    <div class="card-body">
        <form action="{{ route('wali_kelas.rapor.list') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:.85rem;font-weight:600;">Tahun Ajaran <span class="text-danger">*</span></label>
                    <select name="tahun_ajaran_id" class="form-select form-select-sm" required>
                        <option value="">— Pilih Tahun Ajaran —</option>
                        @foreach($tahunAjarans as $t)
                            <option value="{{ $t->id }}" {{ (request('tahun_ajaran_id') == $t->id) ? 'selected' : '' }}>
                                {{ $t->tahun_ajaran }} — {{ ucfirst($t->semester) }}
                                {{ $t->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-search me-1"></i>Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Data Siswa (tampil jika sudah filter) --}}
@if(isset($siswa) && isset($tahunAjaran))
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0" style="font-size:.82rem;">
            <i class="fas fa-calendar-alt me-1"></i>
            {{ $tahunAjaran->tahun_ajaran }} — {{ ucfirst($tahunAjaran->semester) }}
        </p>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary">{{ $siswa->count() }} siswa</span>
            @if($siswa->count() > 0)
                @php
                    $firstKelasId = $siswa->first()->kelas_id;
                @endphp
                <a href="{{ route('wali_kelas.rapor.cetak-kelas', ['kelas_id' => $firstKelasId, 'tahun_ajaran_id' => $tahunAjaran->id]) }}"
                   class="btn btn-sm btn-success" target="_blank">
                    <i class="fas fa-print me-1"></i>Cetak Semua Rapor
                </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th class="text-center" style="width:100px">Jml Nilai</th>
                            <th class="text-center" style="width:100px">Rata-rata</th>
                            <th style="width:180px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $i => $s)
                            @php
                                $avg = $s->nilai->count() > 0 ? round($s->nilai->avg('nilai_angka'), 1) : 0;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td><code>{{ $s->nis }}</code></td>
                                <td>{{ $s->nama_siswa }}</td>
                                <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $s->nilai->count() > 0 ? 'primary' : 'secondary' }}">
                                        {{ $s->nilai->count() }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold">{{ $avg }}</td>
                                <td class="text-center">
                                    <a href="{{ route('wali_kelas.rapor.view', [$s->id, $tahunAjaran->id]) }}"
                                       class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat Rapor">
                                        <i class="fas fa-eye" style="font-size:.7rem;"></i>
                                        <span style="font-size:.78rem;">Rapor</span>
                                    </a>
                                    <a href="{{ route('wali_kelas.rapor.download', [$s->id, $tahunAjaran->id]) }}"
                                       class="btn btn-sm btn-outline-success py-0 px-2" title="Cetak Rapor" target="_blank">
                                        <i class="fas fa-print" style="font-size:.7rem;"></i>
                                        <span style="font-size:.78rem;">Cetak</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Tidak ada siswa ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
