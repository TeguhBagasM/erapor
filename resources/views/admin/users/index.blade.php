@extends('layouts.app')
@section('title', 'Data User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title">Data User</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah User
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="dataTable">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th style="width:120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $i => $u)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>{{ $u->name }}</td>
                            <td><code>{{ $u->email }}</code></td>
                            <td>
                                <span class="badge bg-{{ $u->isAdmin() ? 'danger' : 'primary' }}">
                                    {{ $u->role->name ?? '-' }}
                                </span>
                            </td>
                            <td class="text-muted" style="font-size:.82rem;">{{ $u->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Edit">
                                    <i class="fas fa-pen" style="font-size:.7rem;"></i>
                                </a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus user {{ $u->name }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus">
                                            <i class="fas fa-trash" style="font-size:.7rem;"></i>
                                        </button>
                                    </form>
                                @endif
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
