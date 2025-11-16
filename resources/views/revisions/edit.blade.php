@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Edit Revisi') }}
</h2>
@endsection

@section('content')
<div class="py-6">
	<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
		<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
			<div class="p-6 text-gray-900">
				<form method="POST" action="{{ route('revisions.update', $revision) }}" enctype="multipart/form-data" class="space-y-4">
					@csrf
					@method('PUT')
					
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Mahasiswa</label>
						<select name="mahasiswa_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
							@foreach($mahasiswaList as $m)
								<option value="{{ $m->id }}" @if($revision->mahasiswa_id === $m->id) selected @endif>
									{{ $m->name }} @if($m->nim) ({{ $m->nim }}) @endif
									@if($m->judul_tugas_akhir) - {{ Str::limit($m->judul_tugas_akhir, 50) }} @endif
								</option>
							@endforeach
						</select>
						@error('mahasiswa_id')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Tahap</label>
							<select name="tahap" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
								<option value="proposal" @if($revision->tahap === 'proposal') selected @endif>Proposal</option>
								<option value="sidang_akhir" @if($revision->tahap === 'sidang_akhir') selected @endif>Sidang Akhir</option>
							</select>
							@error('tahap')
								<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Revisi</label>
							<input type="date" name="tanggal_revisi" value="{{ $revision->tanggal_revisi->format('Y-m-d') }}" required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
							@error('tanggal_revisi')
								<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
							@enderror
						</div>
					</div>
					
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Isi Revisi</label>
						<textarea name="isi_revisi" rows="6" required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ $revision->isi_revisi }}</textarea>
						@error('isi_revisi')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
						<select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
							<option value="belum_diperbaiki" @if($revision->status === 'belum_diperbaiki') selected @endif>Belum diperbaiki</option>
							<option value="sudah_diperbaiki" @if($revision->status === 'sudah_diperbaiki') selected @endif>Sudah diperbaiki</option>
						</select>
						@error('status')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti (PDF, opsional)</label>
						<input type="file" name="bukti_file" accept="application/pdf"
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
						@if($revision->bukti_file_path)
							<p class="text-xs mt-1 text-gray-600">File saat ini: 
								<a href="{{ Storage::url($revision->bukti_file_path) }}" target="_blank" class="text-blue-600 hover:underline">
									{{ basename($revision->bukti_file_path) }}
								</a>
							</p>
						@endif
						@error('bukti_file')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>
					
					<div class="pt-4 flex items-center gap-3">
						<button type="submit" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold shadow-md hover:shadow-lg transition">
							<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
							</svg>
							Update Revisi
						</button>
						<a href="{{ route('revisions.show', $revision) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium">
							Batal
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection



