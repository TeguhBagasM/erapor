@extends('layouts.app')
@section('title', 'Pengaturan Mapel per Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Mata Pelajaran per Kelas</h1>
        <p class="text-muted mb-0" style="font-size:.82rem;">Atur mata pelajaran dan guru pengajar untuk setiap kelas</p>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="dataTable">
                <thead>
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Wali Kelas</th>
                        <th class="text-center" style="width:120px">Jml Mapel</th>
                        <th style="width:100px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kelas as $i => $k)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $k->nama_kelas }}</td>
                            <td>{{ $k->jurusan->nama_jurusan ?? '-' }}</td>
                            <td>{{ $k->waliKelas->name ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $k->mataPelajarans->count() > 0 ? 'primary' : 'secondary' }}">
                                    {{ $k->mataPelajarans->count() }} mapel
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.kelas-mapel.edit', $k) }}"
                                   class="btn btn-sm btn-outline-primary py-0 px-2" title="Atur Mapel">
                                    <i class="fas fa-cog" style="font-size:.7rem;"></i>
                                    <span style="font-size:.78rem;">Atur</span>
                                </a>
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
