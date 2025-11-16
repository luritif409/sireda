<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Revisi - {{ $mahasiswa->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h2 {
            font-size: 14px;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        .info-value {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .rekap-card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .rekap-card h3 {
            margin-top: 0;
            color: #333;
            font-size: 16px;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin: 10px 0;
        }
        .stat-item {
            flex: 1;
        }
        .stat-label {
            font-size: 11px;
            color: #666;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .revision-item {
            border-left: 3px solid #4CAF50;
            padding-left: 10px;
            margin-bottom: 15px;
        }
        .revision-item.belum {
            border-left-color: #FF9800;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP REVISI TUGAS AKHIR</h1>
        <p>Mahasiswa: {{ $mahasiswa->name }}</p>
        <p>NIM: {{ $mahasiswa->nim ?? '-' }}</p>
        <p>Tanggal Cetak: {{ date('d F Y, H:i') }}</p>
    </div>

    <div class="info-section">
        <h2>Informasi Mahasiswa</h2>
        <div class="info-row">
            <div class="info-label">Nama:</div>
            <div class="info-value">{{ $mahasiswa->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $mahasiswa->email }}</div>
        </div>
        @if($mahasiswa->nim)
        <div class="info-row">
            <div class="info-label">NIM:</div>
            <div class="info-value">{{ $mahasiswa->nim }}</div>
        </div>
        @endif
        @if($mahasiswa->judul_tugas_akhir)
        <div class="info-row">
            <div class="info-label">Judul Tugas Akhir:</div>
            <div class="info-value">{{ $mahasiswa->judul_tugas_akhir }}</div>
        </div>
        @endif
    </div>

    <div class="info-section">
        <h2>Ringkasan Revisi</h2>
        <div class="stats">
            <div class="stat-item">
                <div class="stat-label">Total Revisi</div>
                <div class="stat-value">{{ $revisions->count() }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Dari Dosen</div>
                <div class="stat-value">{{ $rekapByDosen->count() }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Sudah Diperbaiki</div>
                <div class="stat-value" style="color: #4CAF50;">{{ $revisions->where('status', 'sudah_diperbaiki')->count() }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Belum Diperbaiki</div>
                <div class="stat-value" style="color: #FF9800;">{{ $revisions->where('status', 'belum_diperbaiki')->count() }}</div>
            </div>
        </div>
    </div>

    @foreach($rekapByDosen as $rekap)
    <div class="rekap-card">
        <h3>Rekap dari: {{ $rekap['dosen']->name }}</h3>
        <div class="stats">
            <div class="stat-item">
                <div class="stat-label">Total Revisi</div>
                <div class="stat-value">{{ $rekap['total'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Sudah Diperbaiki</div>
                <div class="stat-value" style="color: #4CAF50;">{{ $rekap['sudah_diperbaiki'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Belum Diperbaiki</div>
                <div class="stat-value" style="color: #FF9800;">{{ $rekap['belum_diperbaiki'] }}</div>
            </div>
            @if($rekap['total'] > 0)
            <div class="stat-item">
                <div class="stat-label">Progress</div>
                <div class="stat-value">{{ round(($rekap['sudah_diperbaiki'] / $rekap['total']) * 100) }}%</div>
            </div>
            @endif
        </div>

        <h4 style="margin-top: 15px; margin-bottom: 10px; font-size: 14px;">Detail Revisi:</h4>
        @foreach($rekap['revisions'] as $rev)
        <div class="revision-item {{ $rev->status === 'belum_diperbaiki' ? 'belum' : '' }}">
            <table style="margin-bottom: 10px;">
                <tr>
                    <td style="width: 120px; font-weight: bold;">Tahap:</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $rev->tahap)) }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Tanggal Revisi:</td>
                    <td>{{ $rev->tanggal_revisi->format('d F Y') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Status:</td>
                    <td>
                        <strong style="color: {{ $rev->status === 'sudah_diperbaiki' ? '#4CAF50' : '#FF9800' }};">
                            {{ str_replace('_', ' ', ucfirst($rev->status)) }}
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Isi Revisi:</td>
                    <td>{{ $rev->isi_revisi }}</td>
                </tr>
            </table>
        </div>
        @endforeach
    </div>
    @endforeach

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis pada {{ date('d F Y, H:i:s') }}</p>
        <p>Sistem Informasi Revisi Tugas Akhir (SIREDA)</p>
    </div>
</body>
</html>





