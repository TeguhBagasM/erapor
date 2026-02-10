@extends('layouts.app')
@section('title', 'Data Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Data Kelas</h1>
    <a href="{{ route('admin.kelas.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah Kelas
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Nama Kelas</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th>Jumlah Siswa</th>
                        <th style="width:120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $i => $k)
                        <tr>
                            <td class="text-muted">{{ $kelas->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $k->nama_kelas }}</td>
                            <td>{{ $k->jurusan->nama_jurusan ?? '-' }}</td>
                            <td>{{ $k->waliKelas->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $k->siswa_count ?? $k->siswa->count() }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.kelas.edit', $k) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Edit">
                                    <i class="fas fa-pen" style="font-size:.7rem;"></i>
                                </a>
                                <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus kelas {{ $k->nama_kelas }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus">
                                        <i class="fas fa-trash" style="font-size:.7rem;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data kelas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($kelas->hasPages())
        <div class="card-footer bg-white border-top-0 pt-0">
            {{ $kelas->links() }}
        </div>
    @endif
</div>
@endsection
