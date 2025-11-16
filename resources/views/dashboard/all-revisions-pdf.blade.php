<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Semua Revisi - {{ $mahasiswa->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #000;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            color: #000;
            font-size: 14px;
        }
        .info-section {
            margin-bottom: 20px;
            border: 1px solid #000;
            padding: 15px;
        }
        .info-section h2 {
            font-size: 16px;
            color: #000;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 6px;
        }
        .info-label {
            font-weight: bold;
            width: 160px;
            font-size: 14px;
        }
        .info-value {
            flex: 1;
            font-size: 14px;
        }
        .revision-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .revision-header {
            border: 1px solid #000;
            padding: 12px;
            margin-bottom: 12px;
        }
        .revision-header h3 {
            margin: 0;
            font-size: 16px;
            color: #000;
            font-weight: bold;
        }
        .revision-header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #000;
        }
        .revision-content {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 15px;
        }
        .revision-meta {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .meta-item {
            display: table-row;
        }
        .meta-label {
            font-size: 13px;
            color: #000;
            padding: 6px 12px 6px 0;
            display: table-cell;
            width: 140px;
            vertical-align: top;
        }
        .meta-value {
            font-weight: normal;
            color: #000;
            font-size: 13px;
            padding: 6px 0;
            display: table-cell;
            vertical-align: top;
        }
        .isi-revisi {
            margin-top: 12px;
            padding: 12px;
            border: 1px solid #000;
        }
        .isi-revisi-label {
            font-weight: normal;
            margin-bottom: 8px;
            color: #000;
            font-size: 14px;
        }
        .isi-revisi-content {
            text-align: justify;
            line-height: 1.8;
            font-size: 14px;
            color: #000;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #000;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .page-break {
            page-break-before: always;
        }
        h4 {
            font-size: 15px;
            color: #000;
            font-weight: normal;
            margin: 0 0 8px 0;
        }
    </style>
</head>
<body>
    @php
        // Tentukan judul berdasarkan tahap revisi
        $tahapCounts = $revisions->groupBy('tahap')->map->count();
        $tahapProposal = $tahapCounts->get('proposal', 0);
        $tahapSidangAkhir = $tahapCounts->get('sidang_akhir', 0);
        
        if ($tahapProposal > 0 && $tahapSidangAkhir == 0) {
            $judul = 'LAPORAN SEMUA REVISI PROPOSAL';
        } elseif ($tahapSidangAkhir > 0 && $tahapProposal == 0) {
            $judul = 'LAPORAN SEMUA REVISI TUGAS AKHIR';
        } elseif ($tahapSidangAkhir >= $tahapProposal) {
            $judul = 'LAPORAN SEMUA REVISI TUGAS AKHIR';
        } else {
            $judul = 'LAPORAN SEMUA REVISI PROPOSAL';
        }
    @endphp
    <div class="header">
        <h1>{{ $judul }}</h1>
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

    @php
        $currentDosenId = null;
        $revisionNumber = 1;
    @endphp

    @foreach($revisions as $rev)
        @if($currentDosenId !== $rev->dosen_id)
            @if($currentDosenId !== null)
                <div class="page-break"></div>
            @endif
            <div class="revision-section">
                <div class="revision-header">
                    <h3>Revisi dari: {{ $rev->dosen->name }}</h3>
                    <p>{{ $rev->dosen->email }}</p>
                </div>
            @php
                $currentDosenId = $rev->dosen_id;
            @endphp
        @endif

        <div class="revision-content">
            <div style="margin-bottom: 12px;">
                <h4>Revisi ke-{{ $revisionNumber++ }}</h4>
            </div>

            <div class="revision-meta">
                <div class="meta-item">
                    <div class="meta-label">Tahap:</div>
                    <div class="meta-value">{{ ucfirst(str_replace('_', ' ', $rev->tahap)) }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Tanggal Revisi:</div>
                    <div class="meta-value">{{ $rev->tanggal_revisi->format('d F Y') }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Tanggal Dibuat:</div>
                    <div class="meta-value">{{ $rev->created_at->format('d F Y, H:i') }}</div>
                </div>
            </div>

            <div class="isi-revisi">
                <div class="isi-revisi-label">Isi Revisi:</div>
                <div class="isi-revisi-content">{{ $rev->isi_revisi }}</div>
            </div>

            @if($rev->bukti_file_path)
            <div style="margin-top: 10px; font-size: 13px; color: #000;">
                <strong>Bukti:</strong> Tersedia ({{ basename($rev->bukti_file_path) }})
            </div>
            @endif
        </div>

        @if($loop->last || ($loop->index < count($revisions) - 1 && $revisions[$loop->index + 1]->dosen_id !== $rev->dosen_id))
            </div>
        @endif
    @endforeach

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis pada {{ date('d F Y, H:i:s') }}</p>
        <p>Sistem Informasi Revisi Tugas Akhir (SIREDA)</p>
        <p>Halaman ini berisi {{ $revisions->count() }} revisi dari {{ $revisions->groupBy('dosen_id')->count() }} dosen</p>
    </div>
</body>
</html>
