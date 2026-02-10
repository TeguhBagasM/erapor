@extends('layouts.app')
@section('title', 'Tahun Ajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Tahun Ajaran</h1>
    <a href="{{ route('admin.tahun-ajaran.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah Tahun Ajaran
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th style="width:120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tahunAjarans as $i => $t)
                        <tr>
                            <td class="text-muted">{{ $tahunAjarans->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $t->tahun_ajaran }}</td>
                            <td>Semester {{ $t->semester }}</td>
                            <td>
                                @if($t->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-50">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.tahun-ajaran.edit', $t) }}" class="btn btn-sm btn-outline-secondary py-0 px-2">
                                    <i class="fas fa-pen" style="font-size:.7rem;"></i>
                                </a>
                                <form action="{{ route('admin.tahun-ajaran.destroy', $t) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus tahun ajaran {{ $t->tahun_ajaran }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2">
                                        <i class="fas fa-trash" style="font-size:.7rem;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data tahun ajaran</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($tahunAjarans->hasPages())
        <div class="card-footer bg-white border-top-0 pt-0">
            {{ $tahunAjarans->links() }}
        </div>
    @endif
</div>
@endsection
