@extends('layouts.app')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Dashboard Mahasiswa') }}
</h2>
    @if($revisions->count() > 0)
    <a href="{{ route('dashboard.mahasiswa.export-all-revisions-pdf') }}" 
       class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 shadow-md transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Export Semua Revisi PDF
    </a>
    @endif
</div>
@endsection

@section('content')
<div class="py-6">
	<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
		@if(auth()->user()->unreadNotifications->count())
			<div class="mb-4 bg-amber-50 border border-amber-200 text-amber-800 p-3 rounded">
				<strong>Notifikasi:</strong>
				<ul class="list-disc ml-5">
					@foreach(auth()->user()->unreadNotifications->take(5) as $notification)
						<li class="text-sm">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<!-- Tombol Export -->
		@if($revisions->count() > 0)
		<div class="mb-6 flex items-center justify-end">
			<a href="{{ route('dashboard.mahasiswa.export-all-revisions-pdf') }}" 
			   class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 shadow-md transition">
				<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
				</svg>
				Export Semua Revisi PDF
			</a>
		</div>
		@endif

		<!-- Rekap Revisi dari Semua Dosen -->
		@if($rekapByDosen->count() > 0)
		<div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
			<div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
				<h3 class="text-lg font-semibold text-gray-900">Rekap Revisi dari Semua Dosen</h3>
			</div>
			<div class="p-6">
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
					@foreach($rekapByDosen as $rekap)
					<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
						<div class="mb-3">
							<h4 class="font-semibold text-gray-900 text-lg mb-1">
								{{ $rekap['dosen']->name }}
							</h4>
							<p class="text-sm text-gray-600">{{ $rekap['dosen']->email }}</p>
						</div>
						
						<div class="space-y-2">
							<div class="flex justify-between items-center">
								<span class="text-sm text-gray-700">Total Revisi:</span>
								<span class="font-semibold text-gray-900">{{ $rekap['total'] }}</span>
							</div>
							<div class="flex justify-between items-center">
								<span class="text-sm text-gray-700">Belum Diperbaiki:</span>
								<span class="font-semibold text-amber-600">{{ $rekap['belum_diperbaiki'] }}</span>
							</div>
							<div class="flex justify-between items-center">
								<span class="text-sm text-gray-700">Sudah Diperbaiki:</span>
								<span class="font-semibold text-green-600">{{ $rekap['sudah_diperbaiki'] }}</span>
							</div>
						</div>

						<!-- Progress Bar -->
						@if($rekap['total'] > 0)
						<div class="mt-3">
							<div class="flex justify-between text-xs text-gray-600 mb-1">
								<span>Progress</span>
								<span>{{ round(($rekap['sudah_diperbaiki'] / $rekap['total']) * 100) }}%</span>
							</div>
							<div class="w-full bg-gray-200 rounded-full h-2">
								<div class="bg-green-600 h-2 rounded-full" 
									 style="width: {{ ($rekap['sudah_diperbaiki'] / $rekap['total']) * 100 }}%"></div>
							</div>
						</div>
						@endif
					</div>
					@endforeach
				</div>
			</div>
		</div>
		@endif

		<!-- Daftar Revisi Detail -->
		<div class="bg-white rounded-lg shadow">
			<div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
				<h3 class="text-lg font-semibold text-gray-900">Daftar Revisi</h3>
			</div>
			<div class="p-6">
				@if($revisions->count() > 0)
		<div class="grid md:grid-cols-2 gap-4">
					@foreach($revisions as $rev)
					<div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm hover:shadow-md transition">
						<div class="flex justify-between items-start mb-3">
							<div>
								<div class="text-sm text-gray-500 mb-1">Dosen</div>
								<div class="font-medium text-gray-900">{{ $rev->dosen->name }}</div>
							</div>
							<span class="px-2 py-1 text-xs font-semibold rounded-full
								{{ $rev->status === 'sudah_diperbaiki' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
								{{ str_replace('_', ' ', ucfirst($rev->status)) }}
							</span>
						</div>
						
						<div class="space-y-1 text-sm text-gray-600 mb-3">
							<div><strong>Tahap:</strong> {{ ucfirst(str_replace('_', ' ', $rev->tahap)) }}</div>
							<div><strong>Tanggal Revisi:</strong> {{ $rev->tanggal_revisi->format('d F Y') }}</div>
							<div><strong>Isi Revisi:</strong> {{ Str::limit($rev->isi_revisi, 100) }}</div>
						</div>

						<div class="flex items-center gap-2 pt-3 border-t border-gray-200">
							<a href="{{ route('revisions.share', $rev->token) }}" 
							   class="text-red-600 hover:underline text-sm font-medium" 
							   target="_blank">
								Buka Share Link
							</a>
							@if($rev->bukti_file_path)
							<span class="text-xs text-gray-500">•</span>
							<a href="{{ asset('storage/' . $rev->bukti_file_path) }}" 
							   class="text-red-600 hover:underline text-sm font-medium" 
							   target="_blank">
								Lihat Bukti
							</a>
							@endif
						</div>
					</div>
					@endforeach
				</div>
				@else
				<div class="text-center py-8">
					<p class="text-gray-500">Tidak ada revisi.</p>
				</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
