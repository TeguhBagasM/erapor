@extends('layouts.app')
@section('title', 'Daftar Rapor')

@section('content')
<div class="mb-3">
    <a href="{{ route('wali_kelas.dashboard') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Daftar Rapor Siswa</h1>
        <p class="text-muted mb-0" style="font-size:.82rem;">
            {{ $tahunAjaran->tahun_ajaran }} — Semester {{ $tahunAjaran->semester }}
        </p>
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
                        <th style="width:150px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswa as $i => $s)
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
                            <td class="text-center">
                                <a href="{{ route('wali_kelas.rapor.view', [$s->id, $tahunAjaran->id]) }}"
                                   class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat Rapor">
                                    <i class="fas fa-eye" style="font-size:.7rem;"></i>
                                    <span style="font-size:.78rem;">Rapor</span>
                                </a>
                                <a href="{{ route('wali_kelas.rapor.download', [$s->id, $tahunAjaran->id]) }}"
                                   class="btn btn-sm btn-outline-secondary py-0 px-2" title="Download">
                                    <i class="fas fa-download" style="font-size:.7rem;"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada siswa ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
