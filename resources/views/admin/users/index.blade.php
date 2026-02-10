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
            <table class="table table-hover mb-0">
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
                    @forelse($users as $i => $u)
                        <tr>
                            <td class="text-muted">{{ $users->firstItem() + $i }}</td>
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
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data user</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white border-top-0 pt-0">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
