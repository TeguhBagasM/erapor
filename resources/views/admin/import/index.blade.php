@extends('layouts.app')
@section('title', 'Import Data')

@section('content')
<div class="mb-3">
    <h1 class="page-title">Import Data</h1>
    <p class="text-muted mb-0" style="font-size:.82rem;">Upload file Excel untuk import data siswa atau nilai</p>
</div>

<div class="row g-3">
    {{-- Import Siswa --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-graduate me-2 text-muted"></i>Import Data Siswa
            </div>
            <div class="card-body">
                <p class="text-muted mb-3" style="font-size:.82rem;">
                    Upload file Excel berisi data siswa. Kolom: <code>nis</code>, <code>nama_siswa</code>, <code>kelas</code>
                </p>

                <form action="{{ route('admin.import.siswa') }}" method="POST" enctype="multipart/form-data" id="formImportSiswa">
                    @csrf
                    <input type="hidden" name="tipe_import" value="siswa">

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">File Excel <span class="text-danger">*</span></label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv"
                               class="form-control form-control-sm @error('file') is-invalid @enderror">
                        @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Format: .xlsx, .xls, .csv (maks 5MB)</div>
                    </div>

                    <div class="d-flex gap-2 align-items-center">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-upload me-1"></i>Import Siswa
                        </button>
                        <a href="{{ route('admin.template.siswa') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download me-1"></i>Download Template
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Import Nilai --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-pen-alt me-2 text-muted"></i>Import Data Nilai
            </div>
            <div class="card-body">
                <p class="text-muted mb-3" style="font-size:.82rem;">
                    Upload file Excel berisi data nilai. Kolom: <code>nis</code>, <code>kode_mapel</code>, <code>nilai_angka</code>
                </p>

                <form action="{{ route('admin.import.nilai') }}" method="POST" enctype="multipart/form-data" id="formImportNilai">
                    @csrf
                    <input type="hidden" name="tipe_import" value="nilai">

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="tahun_ajaran_id" class="form-select form-select-sm @error('tahun_ajaran_id') is-invalid @enderror">
                            <option value="">— Pilih Tahun Ajaran —</option>
                            @foreach($tahunAjarans as $t)
                                <option value="{{ $t->id }}" {{ $t->is_active ? 'selected' : '' }}>
                                    {{ $t->tahun_ajaran }} — {{ ucfirst($t->semester) }}
                                    {{ $t->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">File Excel <span class="text-danger">*</span></label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv"
                               class="form-control form-control-sm @error('file') is-invalid @enderror">
                        @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Format: .xlsx, .xls, .csv (maks 5MB)</div>
                    </div>

                    <div class="d-flex gap-2 align-items-center">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-upload me-1"></i>Import Nilai
                        </button>
                        <a href="{{ route('admin.template.nilai') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download me-1"></i>Download Template
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Info --}}
<div class="card mt-3">
    <div class="card-body py-3">
        <h6 class="mb-2" style="font-size:.85rem;font-weight:700;">
            <i class="fas fa-info-circle me-1 text-muted"></i>Panduan Import
        </h6>
        <ul class="mb-0 text-muted" style="font-size:.82rem;">
            <li>Download template terlebih dahulu, isi sesuai format</li>
            <li><strong>Import Siswa:</strong> Pastikan nama kelas sudah ada di sistem</li>
            <li><strong>Import Nilai:</strong> NIS siswa dan kode mata pelajaran harus sudah terdaftar</li>
            <li>Nilai huruf akan dihitung otomatis dari nilai angka (A ≥ 85, B ≥ 70, C ≥ 60, D ≥ 50, E &lt; 50)</li>
            <li>Data yang sudah ada akan di-update, bukan duplikasi</li>
        </ul>
    </div>
</div>
@endsection
