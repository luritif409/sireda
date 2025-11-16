<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Revisi TA - PDF</title>
	<style>
		body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
		.header { text-align: center; margin-bottom: 16px; }
		.header h1 { margin: 0; font-size: 16px; }
		.header h2, .header h3 { margin: 2px 0; font-size: 13px; }
		.section { margin-bottom: 10px; }
		.label { color: #555; width: 160px; display: inline-block; }
		.value { font-weight: bold; }
		.box { border: 1px solid #333; padding: 10px; min-height: 100px; }
		.signature { margin-top: 32px; text-align: right; }
		.signature img { height: 64px; }
	</style>
</head>
<body>
	<div class="header">
		<h1>UNIVERSITAS NAHDLATUL ULAMA SIDOARJO</h1>
		<h2>FAKULTAS ILMU KOMPUTER</h2>
		<h3>SISTEM REVISI TUGAS AKHIR</h3>
	</div>

	<div class="section">
		<div><span class="label">Nama Mahasiswa</span><span class="value">{{ $revision->mahasiswa->name }}</span></div>
		<div><span class="label">NIM</span><span class="value">{{ $revision->mahasiswa->nim ?? '-' }}</span></div>
		<div><span class="label">Judul Tugas Akhir</span><span class="value">{{ $revision->mahasiswa->judul_tugas_akhir ?? '-' }}</span></div>
		<div><span class="label">Tahap</span><span class="value">{{ strtoupper(str_replace('_',' ', $revision->tahap)) }}</span></div>
		<div><span class="label">Tanggal Revisi</span><span class="value">{{ $revision->tanggal_revisi->format('d/m/Y') }}</span></div>
		<div><span class="label">Status</span><span class="value">{{ strtoupper(str_replace('_',' ', $revision->status)) }}</span></div>
	</div>

	<div class="section">
		<div style="margin-bottom:6px;">Isi Revisi:</div>
		<div class="box">{{ $revision->isi_revisi }}</div>
	</div>

	<div class="signature">
		<div>Dosen Pembimbing,</div>
		@if($revision->dosen->signature_path)
			<div><img src="{{ public_path('storage/'.$revision->dosen->signature_path) }}" alt="Tanda Tangan"></div>
		@else
			<div style="height:64px;"></div>
		@endif
		<div style="margin-top:8px;font-weight:bold;">{{ $revision->dosen->name }}</div>
	</div>
</body>
</html>











