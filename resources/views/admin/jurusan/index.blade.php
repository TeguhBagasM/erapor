@extends('layouts.app')
@section('title', 'Data Jurusan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Data Jurusan</h1>
    <a href="{{ route('admin.jurusans.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah Jurusan
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="dataTable">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Nama Jurusan</th>
                        <th>Jumlah Kelas</th>
                        <th style="width:120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jurusans as $i => $j)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $j->nama_jurusan }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $j->kelas_count }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.jurusans.edit', $j) }}" class="btn btn-sm btn-outline-secondary py-0 px-2">
                                    <i class="fas fa-pen" style="font-size:.7rem;"></i>
                                </a>
                                <form action="{{ route('admin.jurusans.destroy', $j) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus jurusan {{ $j->nama_jurusan }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2">
                                        <i class="fas fa-trash" style="font-size:.7rem;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: -1 }]
    });
});
</script>
@endpush
