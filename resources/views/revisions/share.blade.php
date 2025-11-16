<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Share Revisi</title>
	<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
	<div class="max-w-2xl mx-auto p-6 mt-8 bg-white rounded shadow">
		<h1 class="text-2xl font-semibold mb-3">Revisi Tugas Akhir</h1>
		<div class="text-sm text-gray-500">Mahasiswa</div>
		<div class="font-medium">{{ $revision->mahasiswa->name }} @if($revision->mahasiswa->nim) ({{ $revision->mahasiswa->nim }}) @endif</div>
		<div class="text-sm">Tahap: {{ str_replace('_',' ', $revision->tahap) }}</div>
		<div class="text-sm">Tanggal: {{ $revision->tanggal_revisi->format('Y-m-d') }}</div>
		<div class="text-sm">Status: {{ str_replace('_',' ', $revision->status) }}</div>
		<div class="mt-3 whitespace-pre-wrap">{{ $revision->isi_revisi }}</div>
		<div class="mt-6 text-xs text-gray-500">Dosen: {{ $revision->dosen->name }}</div>
	</div>
</body>
</html>











