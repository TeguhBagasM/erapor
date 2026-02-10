@extends('layouts.app')
@section('title', 'Atur Mapel — ' . $kelas->nama_kelas)

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.kelas-mapel.index') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Atur Mata Pelajaran</h1>
        <p class="text-muted mb-0" style="font-size:.82rem;">
            {{ $kelas->nama_kelas }} — {{ $kelas->jurusan->nama_jurusan ?? '-' }}
            @if($kelas->waliKelas)
                &bull; Wali Kelas: {{ $kelas->waliKelas->name }}
            @endif
        </p>
    </div>
</div>

<form action="{{ route('admin.kelas-mapel.update', $kelas) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-book me-2 text-muted"></i>Daftar Mata Pelajaran</span>
            <small class="text-muted">Centang mapel yang diajarkan di kelas ini</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px" class="text-center">
                                <input type="checkbox" id="checkAll" title="Pilih Semua">
                            </th>
                            <th>Kode</th>
                            <th>Mata Pelajaran</th>
                            <th style="width:280px">Guru Pengajar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allMapel as $mapel)
                            @php
                                $isAssigned = array_key_exists($mapel->id, $assigned);
                                $assignedGuruId = $assigned[$mapel->id] ?? null;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="mapel[]" value="{{ $mapel->id }}"
                                           class="mapel-check" data-row="{{ $mapel->id }}"
                                           {{ $isAssigned ? 'checked' : '' }}>
                                </td>
                                <td><code>{{ $mapel->kode_mapel }}</code></td>
                                <td>{{ $mapel->nama_mapel }}</td>
                                <td>
                                    <select name="guru[{{ $mapel->id }}]"
                                            class="form-select form-select-sm guru-select"
                                            data-row="{{ $mapel->id }}"
                                            {{ !$isAssigned ? 'disabled' : '' }}>
                                        <option value="">— Pilih Guru —</option>
                                        @foreach($allGuru as $guru)
                                            <option value="{{ $guru->id }}"
                                                {{ $assignedGuruId == $guru->id ? 'selected' : '' }}>
                                                {{ $guru->nama_guru }} ({{ $guru->nip }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan
                </button>
                <a href="{{ route('admin.kelas-mapel.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle guru select when checkbox changes
    document.querySelectorAll('.mapel-check').forEach(function(cb) {
        cb.addEventListener('change', function() {
            const row = this.dataset.row;
            const guruSelect = document.querySelector('.guru-select[data-row="' + row + '"]');
            if (guruSelect) {
                guruSelect.disabled = !this.checked;
                if (!this.checked) guruSelect.value = '';
            }
        });
    });

    // Check all
    document.getElementById('checkAll').addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('.mapel-check').forEach(function(cb) {
            cb.checked = checked;
            cb.dispatchEvent(new Event('change'));
        });
    });
});
</script>
@endpush
