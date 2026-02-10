@extends('layouts.app')
@section('title', 'Data Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Data Guru</h1>
    <a href="{{ route('admin.guru.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah Guru
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>NIP</th>
                        <th>Nama Guru</th>
                        <th>Akun User</th>
                        <th style="width:120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guru as $i => $g)
                        <tr>
                            <td class="text-muted">{{ $guru->firstItem() + $i }}</td>
                            <td><code>{{ $g->nip }}</code></td>
                            <td>{{ $g->nama_guru }}</td>
                            <td>{{ $g->user->name ?? '—' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.guru.edit', $g) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Edit">
                                    <i class="fas fa-pen" style="font-size:.7rem;"></i>
                                </a>
                                <form action="{{ route('admin.guru.destroy', $g) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus guru {{ $g->nama_guru }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus">
                                        <i class="fas fa-trash" style="font-size:.7rem;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data guru</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($guru->hasPages())
        <div class="card-footer bg-white border-top-0 pt-0">
            {{ $guru->links() }}
        </div>
    @endif
</div>
@endsection
