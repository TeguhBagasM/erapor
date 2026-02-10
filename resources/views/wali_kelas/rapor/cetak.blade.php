<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor - {{ $siswa->nama_siswa }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page { size: A4; margin: 15mm 20mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; background: #fff; line-height: 1.5; }
        @media print {
            .no-print { display: none !important; }
            .rapor-page { box-shadow: none; margin: 0; padding: 0; page-break-after: always; }
            .rapor-page:last-child { page-break-after: avoid; }
        }
        @media screen {
            body { background: #e9ecef; padding: 20px; }
            .rapor-page { max-width: 210mm; margin: 60px auto 20px; background: #fff; padding: 15mm 20mm; box-shadow: 0 2px 12px rgba(0,0,0,.15); }
        }
        .print-toolbar { position: fixed; top: 0; left: 0; right: 0; background: #343a40; color: #fff; padding: 10px 20px; display: flex; align-items: center; gap: 12px; z-index: 1000; box-shadow: 0 2px 8px rgba(0,0,0,.3); }
        .print-toolbar button { background: #4e73df; color: #fff; border: none; padding: 8px 20px; border-radius: 4px; font-size: 14px; cursor: pointer; }
        .print-toolbar button:hover { background: #3b5fc0; }
        .print-toolbar a { color: #ccc; text-decoration: none; font-size: 14px; }
        .print-toolbar a:hover { color: #fff; }
        .kop-sekolah { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-sekolah h2 { font-size: 16pt; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 2px; }
        .kop-sekolah h3 { font-size: 13pt; font-weight: normal; margin-bottom: 2px; }
        .kop-sekolah p { font-size: 10pt; color: #333; }
        .rapor-title { text-align: center; margin: 20px 0; }
        .rapor-title h3 { font-size: 14pt; text-transform: uppercase; letter-spacing: 3px; border-bottom: 1px solid #000; display: inline-block; padding-bottom: 3px; }
        .info-siswa { margin-bottom: 20px; }
        .info-siswa table { border-collapse: collapse; }
        .info-siswa td { padding: 2px 0; vertical-align: top; }
        .info-siswa td:first-child { width: 160px; font-weight: bold; }
        .info-siswa td:nth-child(2) { width: 15px; text-align: center; }
        .tabel-nilai { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .tabel-nilai th, .tabel-nilai td { border: 1px solid #000; padding: 6px 10px; }
        .tabel-nilai th { background: #f0f0f0; text-align: center; font-size: 11pt; }
        .tabel-nilai td.tc { text-align: center; }
        .tabel-nilai tfoot td { font-weight: bold; background: #f8f8f8; }
        .ringkasan { margin-bottom: 30px; }
        .ringkasan table { border-collapse: collapse; }
        .ringkasan td { padding: 3px 0; }
        .ringkasan td:first-child { width: 200px; }
        .ringkasan td:nth-child(2) { width: 15px; text-align: center; }
        .ttd-section { margin-top: 40px; }
        .ttd-section table { width: 100%; }
        .ttd-section td { text-align: center; vertical-align: top; padding: 5px 0; }
        .ttd-space { height: 70px; }
    </style>
</head>
<body>
    <div class="print-toolbar no-print">
        <button onclick="window.print()"><i class="fas fa-print"></i> Cetak Rapor</button>
        <a href="javascript:history.back()">&larr; Kembali</a>
        <span style="font-size:13px;opacity:.7;margin-left:auto;">
            {{ $siswa->nama_siswa }} — {{ $kelas->nama_kelas }}
        </span>
    </div>

    <div class="rapor-page">
        <div class="kop-sekolah">
            <h3>Pemerintah Daerah</h3>
            <h2>Sekolah Menengah Kejuruan</h2>
            <p>Jl. Pendidikan No. 1 &bull; Telp. (021) 1234567</p>
        </div>

        <div class="rapor-title"><h3>Laporan Hasil Belajar</h3></div>

        <div class="info-siswa">
            <table>
                <tr><td>Nama Siswa</td><td>:</td><td><strong>{{ $siswa->nama_siswa }}</strong></td></tr>
                <tr><td>No. Induk Siswa</td><td>:</td><td>{{ $siswa->nis }}</td></tr>
                <tr><td>Kelas</td><td>:</td><td>{{ $kelas->nama_kelas }}</td></tr>
                <tr><td>Jurusan</td><td>:</td><td>{{ $kelas->jurusan->nama_jurusan ?? '-' }}</td></tr>
                <tr><td>Tahun Ajaran</td><td>:</td><td>{{ $tahunAjaran->tahun_ajaran }}</td></tr>
                <tr><td>Semester</td><td>:</td><td>{{ ucfirst($tahunAjaran->semester) }}</td></tr>
            </table>
        </div>

        @php
            $totalNilai = 0;
            $countNilai = 0;
        @endphp

        <table class="tabel-nilai">
            <thead>
                <tr>
                    <th style="width:40px">No</th>
                    <th>Mata Pelajaran</th>
                    <th style="width:130px">Guru Pengajar</th>
                    <th style="width:90px">Nilai Angka</th>
                    <th style="width:80px">Nilai Huruf</th>
                    <th style="width:110px">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mapels as $i => $mapel)
                    @php
                        $nilai = $nilaiSiswa[$mapel->id] ?? null;
                        $guruNama = $nilai ? ($nilai->guru->nama_guru ?? '-') : ($mapel->pivot->guru_id ?? '-');
                        if ($mapel->pivot && $mapel->pivot->guru_id) {
                            $guruPivot = \App\Models\Guru::find($mapel->pivot->guru_id);
                            $guruNama = $guruPivot->nama_guru ?? $guruNama;
                        }
                        if ($nilai) {
                            $totalNilai += $nilai->nilai_angka;
                            $countNilai++;
                        }
                    @endphp
                    <tr>
                        <td class="tc">{{ $i + 1 }}</td>
                        <td>{{ $mapel->nama_mapel }}</td>
                        <td style="font-size:10pt;">{{ $nilai ? ($nilai->guru->nama_guru ?? '-') : '-' }}</td>
                        <td class="tc">{{ $nilai ? $nilai->nilai_angka : '-' }}</td>
                        <td class="tc">{{ $nilai ? $nilai->nilai_huruf : '-' }}</td>
                        <td class="tc" style="font-size:10pt;">
                            @if($nilai)
                                {{ match($nilai->nilai_huruf) { 'A' => 'Sangat Baik', 'B' => 'Baik', 'C' => 'Cukup', 'D' => 'Kurang', 'E' => 'Sangat Kurang', default => '-' } }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            @if($countNilai > 0)
                @php $rataRata = round($totalNilai / $countNilai, 1); @endphp
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right;"><strong>Rata-rata</strong></td>
                        <td class="tc"><strong>{{ $rataRata }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <div class="ringkasan">
            <table>
                <tr><td>Jumlah Mata Pelajaran</td><td>:</td><td>{{ $mapels->count() }}</td></tr>
                <tr><td>Jumlah Nilai Terisi</td><td>:</td><td>{{ $countNilai }} dari {{ $mapels->count() }}</td></tr>
                @if($countNilai > 0)
                    <tr><td>Rata-rata Nilai</td><td>:</td><td><strong>{{ $rataRata }}</strong></td></tr>
                    <tr><td>Status Kelulusan</td><td>:</td><td><strong>{{ $rataRata >= 70 ? 'LULUS' : 'BELUM LULUS' }}</strong></td></tr>
                @endif
            </table>
        </div>

        <div class="ttd-section">
            <table>
                <tr>
                    <td>Mengetahui,<br>Kepala Sekolah</td>
                    <td></td>
                    <td>Dicetak, {{ date('d F Y') }}<br>Wali Kelas</td>
                </tr>
                <tr><td class="ttd-space"></td><td></td><td class="ttd-space"></td></tr>
                <tr>
                    <td><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br>NIP.</td>
                    <td></td>
                    <td><u>{{ $waliKelas->name }}</u><br>NIP. {{ $waliKelas->guru->nip ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
