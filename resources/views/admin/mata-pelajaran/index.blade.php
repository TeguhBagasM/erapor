@extends('layouts.app')
@section('title', 'Data Mata Pelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Mata Pelajaran</h1>
    <a href="{{ route('admin.mata-pelajaran.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah Mapel
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="dataTable">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th style="width:120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mataPelajarans as $i => $m)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td><code>{{ $m->kode_mapel }}</code></td>
                            <td>{{ $m->nama_mapel }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.mata-pelajaran.edit', $m) }}" class="btn btn-sm btn-outline-secondary py-0 px-2">
                                    <i class="fas fa-pen" style="font-size:.7rem;"></i>
                                </a>
                                <form action="{{ route('admin.mata-pelajaran.destroy', $m) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus mata pelajaran {{ $m->nama_mapel }}?')">
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
