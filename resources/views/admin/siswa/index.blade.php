@extends('layouts.app')
@section('title', 'Data Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Data Siswa</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.template.siswa') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-download me-1"></i>Template Excel
        </a>
        <a href="{{ route('admin.siswa.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Tambah Siswa
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="dataTable">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th style="width:120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswa as $i => $s)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td><code>{{ $s->nis }}</code></td>
                            <td>{{ $s->nama_siswa }}</td>
                            <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.siswa.edit', $s) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Edit">
                                    <i class="fas fa-pen" style="font-size:.7rem;"></i>
                                </a>
                                <form action="{{ route('admin.siswa.destroy', $s) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus siswa {{ $s->nama_siswa }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus">
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
