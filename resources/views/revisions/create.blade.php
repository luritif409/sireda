@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Tambah Revisi Baru') }}
</h2>
@endsection

@section('content')
<div class="py-6">
	<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
		@if($errors->any())
			<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
				<ul class="list-disc list-inside">
					@foreach($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
			<div class="p-6 text-gray-900">
				<form method="POST" action="{{ route('revisions.store') }}" enctype="multipart/form-data" class="space-y-6">
					@csrf
					
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Mahasiswa *</label>
						@if($mahasiswaList->count() > 0)
							<select name="mahasiswa_id" required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
								<option value="">Pilih Mahasiswa</option>
								@foreach($mahasiswaList as $m)
									<option value="{{ $m->id }}" {{ old('mahasiswa_id') == $m->id ? 'selected' : '' }}>
										{{ $m->name }} @if($m->nim) ({{ $m->nim }}) @endif
										@if($m->judul_tugas_akhir) - {{ Str::limit($m->judul_tugas_akhir, 50) }} @endif
									</option>
								@endforeach
							</select>
							@error('mahasiswa_id')
								<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
							@enderror
						@else
							<div class="bg-amber-50 border border-amber-200 rounded-md p-4">
								<p class="text-amber-800 text-sm mb-2">
									<strong>Belum ada mahasiswa terdaftar.</strong>
								</p>
								<p class="text-amber-700 text-xs mb-3">
									Silakan hubungi admin untuk menambahkan mahasiswa terlebih dahulu.
								</p>
								@if(auth()->user()->role === 'admin')
									<a href="{{ route('users.create') }}" class="inline-flex items-center px-3 py-1.5 bg-amber-600 text-white text-xs font-medium rounded hover:bg-amber-700">
										Tambah Mahasiswa
									</a>
								@endif
							</div>
						@endif
					</div>

					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Tahap *</label>
							<select name="tahap" required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
								<option value="">Pilih Tahap</option>
								<option value="proposal" {{ old('tahap') === 'proposal' ? 'selected' : '' }}>Proposal</option>
								<option value="sidang_akhir" {{ old('tahap') === 'sidang_akhir' ? 'selected' : '' }}>Sidang Akhir</option>
							</select>
							@error('tahap')
								<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
							@enderror
						</div>
						<div>
							<label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Revisi *</label>
							<input type="date" name="tanggal_revisi" value="{{ old('tanggal_revisi', date('Y-m-d')) }}" required
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
							@error('tanggal_revisi')
								<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Isi Revisi *</label>
						<textarea name="isi_revisi" rows="8" required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
							placeholder="Masukkan isi revisi yang perlu diperbaiki oleh mahasiswa...">{{ old('isi_revisi') }}</textarea>
						@error('isi_revisi')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
						<select name="status" required
							class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
							<option value="belum_diperbaiki" {{ old('status', 'belum_diperbaiki') === 'belum_diperbaiki' ? 'selected' : '' }}>Belum diperbaiki</option>
							<option value="sudah_diperbaiki" {{ old('status') === 'sudah_diperbaiki' ? 'selected' : '' }}>Sudah diperbaiki</option>
						</select>
						@error('status')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>

					<div class="pt-4 flex items-center gap-3 border-t border-gray-200">
						<button type="submit" class="px-5 py-2.5 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 text-sm font-semibold shadow-md">
							Simpan Revisi
						</button>
						<a href="{{ route('revisions.index') }}" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-semibold">
							Batal
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection



