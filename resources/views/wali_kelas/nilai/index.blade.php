@extends('layouts.app')
@section('title', 'Input Nilai')

@section('content')
<div class="mb-3">
    <h1 class="page-title">Input Nilai</h1>
    <p class="text-muted mb-0" style="font-size:.82rem;">Pilih kelas, mata pelajaran, dan tahun ajaran</p>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-filter me-2 text-muted"></i>Filter Data
    </div>
    <div class="card-body">
        <form action="{{ route('wali_kelas.nilai.create') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:.85rem;font-weight:600;">Kelas <span class="text-danger">*</span></label>
                    <select name="kelas_id" class="form-select form-select-sm @error('kelas_id') is-invalid @enderror" required>
                        <option value="">— Pilih Kelas —</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:.85rem;font-weight:600;">Mata Pelajaran <span class="text-danger">*</span></label>
                    <select name="mata_pelajaran_id" class="form-select form-select-sm @error('mata_pelajaran_id') is-invalid @enderror" required>
                        <option value="">— Pilih Mapel —</option>
                        @foreach($mataPelajarans as $m)
                            <option value="{{ $m->id }}" {{ old('mata_pelajaran_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                    @error('mata_pelajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:.85rem;font-weight:600;">Tahun Ajaran <span class="text-danger">*</span></label>
                    <select name="tahun_ajaran_id" class="form-select form-select-sm @error('tahun_ajaran_id') is-invalid @enderror" required>
                        @foreach($tahunAjarans as $t)
                            <option value="{{ $t->id }}" {{ old('tahun_ajaran_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->tahun_ajaran }} — Smt {{ $t->semester }}
                                {{ $t->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('tahun_ajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-arrow-right me-1"></i>Tampilkan Siswa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
