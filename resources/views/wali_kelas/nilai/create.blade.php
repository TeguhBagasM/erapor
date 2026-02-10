@extends('layouts.app')
@section('title', 'Input Nilai — ' . $kelas->nama_kelas)

@section('content')
<div class="mb-3">
    <a href="{{ route('wali_kelas.nilai.index') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali ke Filter
    </a>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="page-title">Input Nilai</h1>
        <p class="text-muted mb-0" style="font-size:.82rem;">
            {{ $kelas->nama_kelas }} &bull; {{ $mataPelajaran->nama_mapel }} &bull;
            {{ $tahunAjaran->tahun_ajaran }} Smt {{ $tahunAjaran->semester }}
        </p>
    </div>
</div>

<form action="{{ route('wali_kelas.nilai.store') }}" method="POST" id="formNilai">
    @csrf
    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
    <input type="hidden" name="mata_pelajaran_id" value="{{ $mataPelajaran->id }}">
    <input type="hidden" name="tahun_ajaran_id" value="{{ $tahunAjaran->id }}">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-pen-alt me-2 text-muted"></i>Daftar Siswa</span>
            <small class="text-muted">{{ $kelas->siswa->count() }} siswa</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th style="width:150px" class="text-center">Nilai Angka</th>
                            <th style="width:100px" class="text-center">Nilai Huruf</th>
                            <th style="width:80px" class="text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas->siswa->sortBy('nama_siswa')->values() as $i => $siswa)
                            @php
                                $existing = $existingNilai[$siswa->id] ?? null;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td><code>{{ $siswa->nis }}</code></td>
                                <td>{{ $siswa->nama_siswa }}</td>
                                <td class="text-center">
                                    <input type="hidden" name="nilai[{{ $i }}][siswa_id]" value="{{ $siswa->id }}">
                                    <input type="number" name="nilai[{{ $i }}][nilai_angka]"
                                           min="0" max="100" step="1"
                                           class="form-control form-control-sm text-center nilai-input"
                                           value="{{ old("nilai.{$i}.nilai_angka", $existing->nilai_angka ?? '') }}"
                                           placeholder="0-100">
                                </td>
                                <td class="text-center nilai-huruf {{ $existing ? '' : 'text-muted' }}">
                                    {{ $existing->nilai_huruf ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('wali_kelas.nilai.show', $siswa->id) }}"
                                       class="btn btn-sm btn-outline-secondary py-0 px-2" title="Rekap Nilai">
                                        <i class="fas fa-eye" style="font-size:.7rem;"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Tidak ada siswa di kelas ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan Nilai
                </button>
                <a href="{{ route('wali_kelas.nilai.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function getHuruf(angka) {
        if (angka >= 85) return 'A';
        if (angka >= 70) return 'B';
        if (angka >= 60) return 'C';
        if (angka >= 50) return 'D';
        return 'E';
    }

    document.querySelectorAll('.nilai-input').forEach(function(input) {
        input.addEventListener('input', function() {
            const hurufCell = this.closest('tr').querySelector('.nilai-huruf');
            const val = parseInt(this.value);
            if (!isNaN(val) && val >= 0 && val <= 100) {
                hurufCell.textContent = getHuruf(val);
                hurufCell.classList.remove('text-muted');
            } else {
                hurufCell.textContent = '-';
                hurufCell.classList.add('text-muted');
            }
        });
    });
});
</script>
@endpush
