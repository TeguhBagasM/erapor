@extends('layouts.app')
@section('title', 'Input Nilai')

@section('content')
<div class="mb-3">
    <h1 class="page-title">Input Nilai</h1>
    <p class="text-muted mb-0" style="font-size:.82rem;">Masukkan nilai siswa per kelas dan mata pelajaran</p>
</div>

{{-- Filter Form --}}
<div class="card mb-3">
    <div class="card-body">
        <form id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.85rem;font-weight:600;">Kelas <span class="text-danger">*</span></label>
                    <select id="kelas_id" class="form-select form-select-sm" required>
                        <option value="">— Pilih Kelas —</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.85rem;font-weight:600;">Mata Pelajaran <span class="text-danger">*</span></label>
                    <select id="mata_pelajaran_id" class="form-select form-select-sm" required>
                        <option value="">— Pilih Mapel —</option>
                        @foreach($mataPelajarans as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.85rem;font-weight:600;">Guru Pengajar <span class="text-danger">*</span></label>
                    <select id="guru_id" class="form-select form-select-sm" required>
                        <option value="">— Pilih Guru —</option>
                        @foreach($guru as $g)
                            <option value="{{ $g->id }}">{{ $g->nama_guru }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" style="font-size:.85rem;font-weight:600;">Tahun Ajaran</label>
                    <select id="tahun_ajaran_id" class="form-select form-select-sm" required>
                        @foreach($tahunAjarans as $t)
                            <option value="{{ $t->id }}" {{ $t->is_active ? 'selected' : '' }}>
                                {{ $t->tahun_ajaran }} — Smt {{ $t->semester }}
                                {{ $t->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-sm btn-primary" id="btnLoadSiswa">
                    <i class="fas fa-search me-1"></i>Tampilkan Siswa
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Input Nilai --}}
<div class="card d-none" id="nilaiCard">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-pen-alt me-2 text-muted"></i>Input Nilai</span>
        <small class="text-muted" id="infoLabel"></small>
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
                    </tr>
                </thead>
                <tbody id="nilaiBody">
                    {{-- Diisi via JavaScript --}}
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <div class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-sm btn-primary" id="btnSimpan">
                <i class="fas fa-save me-1"></i>Simpan Nilai
            </button>
            <a href="{{ route('admin.import.form') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-file-excel me-1"></i>Import Excel
            </a>
            <a href="{{ route('admin.template.nilai') }}" class="btn btn-sm link-secondary text-decoration-none" style="font-size:.82rem;">
                <i class="fas fa-download me-1"></i>Download Template
            </a>
        </div>
    </div>
</div>

{{-- Loading --}}
<div class="text-center py-5 d-none" id="loadingSpinner">
    <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
    <p class="text-muted mt-2" style="font-size:.82rem;">Memuat data siswa...</p>
</div>

{{-- Alert --}}
<div class="d-none" id="alertContainer">
    <div class="alert alert-dismissible fade show" role="alert" id="alertBox">
        <span id="alertMessage"></span>
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const nilaiCard = document.getElementById('nilaiCard');
    const nilaiBody = document.getElementById('nilaiBody');
    const infoLabel = document.getElementById('infoLabel');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const btnSimpan = document.getElementById('btnSimpan');
    const alertContainer = document.getElementById('alertContainer');
    const alertBox = document.getElementById('alertBox');
    const alertMessage = document.getElementById('alertMessage');

    function getHuruf(angka) {
        if (angka >= 80) return 'A';
        if (angka >= 70) return 'B';
        if (angka >= 60) return 'C';
        if (angka >= 50) return 'D';
        return 'E';
    }

    function showAlert(message, type = 'success') {
        alertContainer.classList.remove('d-none');
        alertBox.className = 'alert alert-' + type + ' alert-dismissible fade show';
        alertMessage.textContent = message;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Load siswa by kelas
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const kelasId = document.getElementById('kelas_id').value;
        if (!kelasId) return;

        loadingSpinner.classList.remove('d-none');
        nilaiCard.classList.add('d-none');

        // Fetch siswa for the selected kelas
        fetch('/api/kelas/' + kelasId + '/siswa')
            .then(r => r.json())
            .then(data => {
                loadingSpinner.classList.add('d-none');
                renderSiswaTable(data);
            })
            .catch(() => {
                // Fallback: use pre-loaded data or show message
                loadingSpinner.classList.add('d-none');
                showAlert('Gagal memuat data siswa. Pastikan API endpoint tersedia.', 'danger');
            });
    });

    function renderSiswaTable(siswaList) {
        nilaiBody.innerHTML = '';

        if (!siswaList || siswaList.length === 0) {
            nilaiBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada siswa di kelas ini</td></tr>';
            nilaiCard.classList.remove('d-none');
            return;
        }

        const mapelText = document.getElementById('mata_pelajaran_id').selectedOptions[0]?.text || '';
        infoLabel.textContent = mapelText + ' — ' + siswaList.length + ' siswa';

        siswaList.forEach(function(siswa, index) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-muted">${index + 1}</td>
                <td><code>${siswa.nis}</code></td>
                <td>${siswa.nama_siswa}</td>
                <td class="text-center">
                    <input type="number" min="0" max="100" step="1"
                           class="form-control form-control-sm text-center nilai-input"
                           data-siswa-id="${siswa.id}"
                           placeholder="0-100">
                </td>
                <td class="text-center nilai-huruf text-muted">-</td>
            `;
            nilaiBody.appendChild(tr);
        });

        // Auto-calculate huruf
        nilaiBody.querySelectorAll('.nilai-input').forEach(function(input) {
            input.addEventListener('input', function() {
                // Paksa batas 0-100
                let val = parseInt(this.value);
                if (!isNaN(val)) {
                    if (val > 100) { this.value = 100; val = 100; }
                    if (val < 0) { this.value = 0; val = 0; }
                }

                const hurufCell = this.closest('tr').querySelector('.nilai-huruf');
                if (!isNaN(val) && val >= 0 && val <= 100) {
                    hurufCell.textContent = getHuruf(val);
                    hurufCell.classList.remove('text-muted');
                } else {
                    hurufCell.textContent = '-';
                    hurufCell.classList.add('text-muted');
                }
            });
        });

        nilaiCard.classList.remove('d-none');
    }

    // Simpan nilai
    btnSimpan.addEventListener('click', function() {
        const nilaiInputs = nilaiBody.querySelectorAll('.nilai-input');
        const guruId = document.getElementById('guru_id').value;
        const mataPelajaranId = document.getElementById('mata_pelajaran_id').value;
        const tahunAjaranId = document.getElementById('tahun_ajaran_id').value;

        if (!guruId || !mataPelajaranId || !tahunAjaranId) {
            showAlert('Lengkapi filter terlebih dahulu', 'warning');
            return;
        }

        const nilaiData = [];
        nilaiInputs.forEach(function(input) {
            const val = parseInt(input.value);
            if (!isNaN(val) && val >= 0 && val <= 100) {
                nilaiData.push({
                    siswa_id: input.dataset.siswaId,
                    nilai_angka: val
                });
            }
        });

        if (nilaiData.length === 0) {
            showAlert('Masukkan minimal satu nilai', 'warning');
            return;
        }

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

        fetch('{{ route("admin.nilai.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                guru_id: guruId,
                mata_pelajaran_id: mataPelajaranId,
                tahun_ajaran_id: tahunAjaranId,
                nilai: nilaiData
            })
        })
        .then(r => r.json())
        .then(data => {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Nilai';

            if (data.success) {
                showAlert(data.message, 'success');
            } else {
                showAlert(data.message || 'Gagal menyimpan nilai', 'danger');
            }
        })
        .catch(err => {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Nilai';
            showAlert('Terjadi kesalahan saat menyimpan nilai', 'danger');
        });
    });
});
</script>
@endpush
