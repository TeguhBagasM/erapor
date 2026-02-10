@extends('layouts.app')
@section('title', 'Input Nilai')

@section('content')
<div class="mb-3">
    <h1 class="page-title">Input Nilai</h1>
    <p class="text-muted mb-0" style="font-size:.82rem;">Pilih kelas & tahun ajaran, lalu isi seluruh nilai mata pelajaran sekaligus</p>
</div>

{{-- Filter Form --}}
<div class="card mb-3">
    <div class="card-body">
        <form id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:.85rem;font-weight:600;">Kelas <span class="text-danger">*</span></label>
                    <select id="kelas_id" class="form-select form-select-sm" required>
                        <option value="">— Pilih Kelas —</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:.85rem;font-weight:600;">Tahun Ajaran <span class="text-danger">*</span></label>
                    <select id="tahun_ajaran_id" class="form-select form-select-sm" required>
                        <option value="">— Pilih Tahun Ajaran —</option>
                        @foreach($tahunAjarans as $t)
                            <option value="{{ $t->id }}" {{ $t->is_active ? 'selected' : '' }}>
                                {{ $t->tahun_ajaran }} — {{ ucfirst($t->semester) }}
                                {{ $t->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-sm btn-primary" id="btnLoad">
                        <i class="fas fa-table me-1"></i>Tampilkan Grid Nilai
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Empty state --}}
<div id="emptyState" class="text-center py-5">
    <i class="fas fa-th-large text-muted" style="font-size:2.5rem;"></i>
    <p class="text-muted mt-3" style="font-size:.85rem;">Pilih kelas dan tahun ajaran untuk menampilkan grid input nilai</p>
</div>

{{-- No Mapel Warning --}}
<div class="alert alert-warning d-none" id="noMapelAlert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <strong>Belum ada mata pelajaran</strong> yang di-assign ke kelas ini.
    Hubungi admin untuk mengatur mata pelajaran per kelas.
</div>

{{-- Loading --}}
<div class="text-center py-5 d-none" id="loadingSpinner">
    <div class="spinner-border spinner-border-sm text-muted" role="status"></div>
    <p class="text-muted mt-2" style="font-size:.82rem;">Memuat data...</p>
</div>

{{-- Grid Table --}}
<div class="card d-none" id="gridCard">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-pen-alt me-2 text-muted"></i>Grid Input Nilai</span>
        <small class="text-muted" id="gridInfo"></small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height:70vh; overflow:auto;">
            <table class="table table-bordered table-hover mb-0" id="gridTable">
                <thead class="table-light" id="gridHead" style="position:sticky;top:0;z-index:2;">
                </thead>
                <tbody id="gridBody">
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white d-flex gap-2 align-items-center flex-wrap">
        <button type="button" class="btn btn-sm btn-primary" id="btnSimpan">
            <i class="fas fa-save me-1"></i>Simpan Semua Nilai
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnReset">
            <i class="fas fa-undo me-1"></i>Reset Perubahan
        </button>
        <div class="ms-auto d-flex align-items-center gap-3" style="font-size:.78rem;">
            <span><span class="d-inline-block rounded" style="width:12px;height:12px;background:#f0fff0;border:1px solid #198754;"></span> Tersimpan</span>
            <span><span class="d-inline-block rounded" style="width:12px;height:12px;background:#fff3cd;border:1px solid #ffc107;"></span> Belum disimpan</span>
            <span class="text-muted fw-bold" id="statusText"></span>
        </div>
    </div>
</div>

{{-- Alert --}}
<div class="d-none" id="alertContainer">
    <div class="alert alert-dismissible fade show mt-3" role="alert" id="alertBox">
        <span id="alertMessage"></span>
        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
    </div>
</div>

<style>
    #gridTable th, #gridTable td { font-size: .8rem; vertical-align: middle; white-space: nowrap; }
    #gridTable thead th { background: #f8f9fa; }
    #gridTable .mapel-header {
        writing-mode: vertical-lr;
        transform: rotate(180deg);
        min-height: 110px;
        font-weight: 600;
        font-size: .73rem;
        padding: 4px 2px;
    }
    #gridTable .guru-sub { font-size: .65rem; color: #6c757d; font-weight: 400; }
    #gridTable input.grid-input {
        width: 55px; text-align: center; padding: 2px 4px; font-size: .8rem;
        border: 1px solid #dee2e6; border-radius: 3px; transition: all .15s;
    }
    #gridTable input.grid-input:focus {
        border-color: #86b7fe; outline: 0;
        box-shadow: 0 0 0 .15rem rgba(13,110,253,.25);
    }
    #gridTable input.grid-input.has-value { background-color: #f0fff0; border-color: #198754; }
    #gridTable input.grid-input.is-changed { background-color: #fff3cd; border-color: #ffc107; }
    #gridTable .sticky-col { position: sticky; background: #fff; z-index: 1; }
    #gridTable .sticky-col-0 { left: 0; min-width: 40px; }
    #gridTable .sticky-col-1 { left: 40px; min-width: 80px; }
    #gridTable .sticky-col-2 { left: 120px; min-width: 160px; border-right: 2px solid #dee2e6 !important; }
    #gridTable thead .sticky-col { background: #f8f9fa; z-index: 3; }
    #gridTable tr:hover td { background-color: #f1f6ff; }
    #gridTable tr:hover .sticky-col { background-color: #f1f6ff; }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const gridCard = document.getElementById('gridCard');
    const gridHead = document.getElementById('gridHead');
    const gridBody = document.getElementById('gridBody');
    const gridInfo = document.getElementById('gridInfo');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const emptyState = document.getElementById('emptyState');
    const noMapelAlert = document.getElementById('noMapelAlert');
    const btnSimpan = document.getElementById('btnSimpan');
    const btnReset = document.getElementById('btnReset');
    const alertContainer = document.getElementById('alertContainer');
    const alertBox = document.getElementById('alertBox');
    const alertMessage = document.getElementById('alertMessage');
    const statusText = document.getElementById('statusText');

    let currentMapels = [];
    let currentSiswa = [];
    let existingNilai = {};

    function getHuruf(n) {
        if (n >= 85) return 'A';
        if (n >= 70) return 'B';
        if (n >= 60) return 'C';
        if (n >= 50) return 'D';
        return 'E';
    }

    function showAlert(message, type) {
        alertContainer.classList.remove('d-none');
        alertBox.className = 'alert alert-' + type + ' alert-dismissible fade show mt-3';
        alertMessage.innerHTML = message;
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }

    function updateStatus() {
        const inputs = gridBody.querySelectorAll('.grid-input');
        let filled = 0, changed = 0;
        inputs.forEach(inp => {
            if (inp.value !== '') filled++;
            if (inp.value !== inp.dataset.original) changed++;
        });
        statusText.textContent = filled + '/' + inputs.length + ' terisi' + (changed > 0 ? ' | ' + changed + ' berubah' : '');
    }

    // Load grid data
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const kelasId = document.getElementById('kelas_id').value;
        const tahunAjaranId = document.getElementById('tahun_ajaran_id').value;
        if (!kelasId || !tahunAjaranId) return;

        emptyState.classList.add('d-none');
        noMapelAlert.classList.add('d-none');
        gridCard.classList.add('d-none');
        alertContainer.classList.add('d-none');
        loadingSpinner.classList.remove('d-none');

        fetch('/api/kelas/' + kelasId + '/grid-nilai?tahun_ajaran_id=' + tahunAjaranId)
            .then(r => r.json())
            .then(data => {
                loadingSpinner.classList.add('d-none');

                if (!data.mapels || data.mapels.length === 0) {
                    noMapelAlert.classList.remove('d-none');
                    return;
                }
                if (!data.siswa || data.siswa.length === 0) {
                    showAlert('Tidak ada siswa di kelas ini', 'warning');
                    return;
                }

                currentMapels = data.mapels;
                currentSiswa = data.siswa;
                existingNilai = data.nilai || {};
                renderGrid();
            })
            .catch(() => {
                loadingSpinner.classList.add('d-none');
                showAlert('Gagal memuat data. Pastikan kelas sudah di-assign mata pelajaran.', 'danger');
            });
    });

    function renderGrid() {
        // Header
        let headHtml = '<tr>';
        headHtml += '<th class="sticky-col sticky-col-0 text-center">#</th>';
        headHtml += '<th class="sticky-col sticky-col-1">NIS</th>';
        headHtml += '<th class="sticky-col sticky-col-2">Nama Siswa</th>';
        currentMapels.forEach(function(m) {
            headHtml += '<th class="text-center" style="min-width:70px;">' +
                '<div class="mapel-header">' + m.nama_mapel + '</div>' +
                '<div class="guru-sub">' + m.guru_nama + '</div>' +
                '</th>';
        });
        headHtml += '<th class="text-center" style="min-width:65px;">Rata²</th>';
        headHtml += '</tr>';
        gridHead.innerHTML = headHtml;

        // Body
        let bodyHtml = '';
        currentSiswa.forEach(function(s, idx) {
            bodyHtml += '<tr>';
            bodyHtml += '<td class="sticky-col sticky-col-0 text-center text-muted">' + (idx + 1) + '</td>';
            bodyHtml += '<td class="sticky-col sticky-col-1"><code style="font-size:.75rem;">' + s.nis + '</code></td>';
            bodyHtml += '<td class="sticky-col sticky-col-2" style="font-size:.78rem;">' + s.nama_siswa + '</td>';

            currentMapels.forEach(function(m) {
                const key = s.id + '-' + m.id;
                const existing = existingNilai[key];
                const val = existing ? Math.round(existing.nilai_angka) : '';
                const cls = existing ? 'has-value' : '';
                bodyHtml += '<td class="text-center p-1">' +
                    '<input type="number" min="0" max="100" step="1" ' +
                    'class="grid-input ' + cls + '" ' +
                    'data-siswa-id="' + s.id + '" ' +
                    'data-mapel-id="' + m.id + '" ' +
                    'data-guru-id="' + (m.guru_id || '') + '" ' +
                    'data-original="' + val + '" ' +
                    'value="' + val + '">' +
                    '</td>';
            });

            bodyHtml += '<td class="text-center fw-bold rata-rata" data-siswa="' + s.id + '" style="font-size:.78rem;">-</td>';
            bodyHtml += '</tr>';
        });
        gridBody.innerHTML = bodyHtml;

        // Info label
        const kelasText = document.getElementById('kelas_id').selectedOptions[0]?.text || '';
        gridInfo.textContent = kelasText + ' — ' + currentSiswa.length + ' siswa × ' + currentMapels.length + ' mapel';

        // Input events
        gridBody.querySelectorAll('.grid-input').forEach(function(input) {
            input.addEventListener('input', function() {
                let val = parseInt(this.value);
                if (!isNaN(val)) {
                    if (val > 100) this.value = 100;
                    if (val < 0) this.value = 0;
                }
                this.classList.toggle('has-value', this.value !== '');
                this.classList.toggle('is-changed', this.value !== this.dataset.original);
                updateAvg(this.dataset.siswaId);
                updateStatus();
            });

            // Keyboard navigation
            input.addEventListener('keydown', function(e) {
                const allInputs = Array.from(gridBody.querySelectorAll('.grid-input'));
                const ci = allInputs.indexOf(this);
                const cols = currentMapels.length;

                if (e.key === 'ArrowRight' || (e.key === 'Tab' && !e.shiftKey)) {
                    e.preventDefault();
                    if (ci + 1 < allInputs.length) allInputs[ci + 1].focus();
                } else if (e.key === 'ArrowLeft' || (e.key === 'Tab' && e.shiftKey)) {
                    e.preventDefault();
                    if (ci - 1 >= 0) allInputs[ci - 1].focus();
                } else if (e.key === 'ArrowDown' || e.key === 'Enter') {
                    e.preventDefault();
                    if (ci + cols < allInputs.length) allInputs[ci + cols].focus();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (ci - cols >= 0) allInputs[ci - cols].focus();
                }
            });
        });

        // Calc initial averages
        currentSiswa.forEach(s => updateAvg(s.id));
        updateStatus();
        gridCard.classList.remove('d-none');
    }

    function updateAvg(siswaId) {
        const inputs = gridBody.querySelectorAll('.grid-input[data-siswa-id="' + siswaId + '"]');
        let sum = 0, count = 0;
        inputs.forEach(inp => {
            const v = parseFloat(inp.value);
            if (!isNaN(v)) { sum += v; count++; }
        });
        const cell = gridBody.querySelector('.rata-rata[data-siswa="' + siswaId + '"]');
        if (count > 0) {
            const avg = (sum / count).toFixed(1);
            cell.textContent = avg + ' (' + getHuruf(parseFloat(avg)) + ')';
        } else {
            cell.textContent = '-';
        }
    }

    // Reset changes
    btnReset.addEventListener('click', function() {
        gridBody.querySelectorAll('.grid-input').forEach(inp => {
            inp.value = inp.dataset.original || '';
            inp.classList.remove('is-changed');
            inp.classList.toggle('has-value', inp.value !== '');
            updateAvg(inp.dataset.siswaId);
        });
        updateStatus();
    });

    // Save all
    btnSimpan.addEventListener('click', function() {
        const kelasId = document.getElementById('kelas_id').value;
        const tahunAjaranId = document.getElementById('tahun_ajaran_id').value;

        if (!kelasId || !tahunAjaranId) {
            showAlert('Pilih kelas dan tahun ajaran terlebih dahulu', 'warning');
            return;
        }

        const nilaiData = [];
        gridBody.querySelectorAll('.grid-input').forEach(function(inp) {
            const val = parseFloat(inp.value);
            if (!isNaN(val) && val >= 0 && val <= 100 && inp.dataset.guruId) {
                nilaiData.push({
                    siswa_id: parseInt(inp.dataset.siswaId),
                    mata_pelajaran_id: parseInt(inp.dataset.mapelId),
                    guru_id: parseInt(inp.dataset.guruId),
                    nilai_angka: val
                });
            }
        });

        if (nilaiData.length === 0) {
            showAlert('Masukkan minimal satu nilai', 'warning');
            return;
        }

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan ' + nilaiData.length + ' nilai...';

        fetch('{{ route("wali_kelas.nilai.store-grid") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                kelas_id: parseInt(kelasId),
                tahun_ajaran_id: parseInt(tahunAjaranId),
                nilai: nilaiData
            })
        })
        .then(r => r.json())
        .then(data => {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Semua Nilai';

            if (data.success) {
                showAlert('<i class="fas fa-check-circle me-1"></i>' + data.message, 'success');
                gridBody.querySelectorAll('.grid-input').forEach(inp => {
                    if (inp.value !== '') {
                        inp.dataset.original = inp.value;
                        inp.classList.remove('is-changed');
                        inp.classList.add('has-value');
                    }
                });
                updateStatus();
            } else {
                showAlert(data.message || 'Gagal menyimpan nilai', 'danger');
            }
        })
        .catch(() => {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Semua Nilai';
            showAlert('Terjadi kesalahan saat menyimpan nilai', 'danger');
        });
    });
});
</script>
@endpush
