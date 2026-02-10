@extends('layouts.app')
@section('title', isset($tahunAjaran) ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.tahun-ajaran.index') }}" class="text-decoration-none" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Kembali ke Tahun Ajaran
    </a>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                {{ isset($tahunAjaran) ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran Baru' }}
            </div>
            <div class="card-body">
                <form action="{{ isset($tahunAjaran) ? route('admin.tahun-ajaran.update', $tahunAjaran) : route('admin.tahun-ajaran.store') }}"
                      method="POST">
                    @csrf
                    @if(isset($tahunAjaran)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" name="tahun_ajaran" class="form-control form-control-sm @error('tahun_ajaran') is-invalid @enderror"
                               value="{{ old('tahun_ajaran', $tahunAjaran->tahun_ajaran ?? '') }}" placeholder="Contoh: 2025/2026">
                        @error('tahun_ajaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-size:.85rem;font-weight:600;">Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select form-select-sm @error('semester') is-invalid @enderror">
                            <option value="">— Pilih Semester —</option>
                            <option value="1" {{ old('semester', $tahunAjaran->semester ?? '') == '1' ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                            <option value="2" {{ old('semester', $tahunAjaran->semester ?? '') == '2' ? 'selected' : '' }}>Semester 2 (Genap)</option>
                        </select>
                        @error('semester') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $tahunAjaran->is_active ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active" style="font-size:.85rem;">
                                Tahun ajaran aktif
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save me-1"></i>{{ isset($tahunAjaran) ? 'Update' : 'Simpan' }}
                        </button>
                        <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-sm btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
