@extends('layouts.app')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Daftar Revisi') }}
    </h2>
    <a href="{{ route('revisions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Buat Revisi
    </a>
</div>
@endsection

@section('content')
<div class="py-6">
	<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
		@if(session('success'))
			<div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
				<span class="block sm:inline">{{ session('success') }}</span>
			</div>
		@endif
		@if(session('error'))
			<div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
				<span class="block sm:inline">{{ session('error') }}</span>
			</div>
		@endif
		<!-- Filter -->
		<div class="bg-white rounded-lg shadow p-4 mb-6">
			<form method="GET" action="{{ route('revisions.index') }}" class="flex flex-wrap gap-4">
				<div>
					<select name="tahap" class="px-3 py-2 border border-gray-300 rounded-md">
						<option value="">Semua Tahap</option>
						<option value="proposal" {{ request('tahap') === 'proposal' ? 'selected' : '' }}>Proposal</option>
						<option value="sidang_akhir" {{ request('tahap') === 'sidang_akhir' ? 'selected' : '' }}>Sidang Akhir</option>
					</select>
				</div>
				<div>
					<select name="status" class="px-3 py-2 border border-gray-300 rounded-md">
						<option value="">Semua Status</option>
						<option value="belum_diperbaiki" {{ request('status') === 'belum_diperbaiki' ? 'selected' : '' }}>Belum diperbaiki</option>
						<option value="sudah_diperbaiki" {{ request('status') === 'sudah_diperbaiki' ? 'selected' : '' }}>Sudah diperbaiki</option>
					</select>
				</div>
				<button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
					Filter
				</button>
				@if(request('tahap') || request('status'))
					<a href="{{ route('revisions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
						Reset
					</a>
				@endif
			</form>
		</div>

		<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
			<div class="p-6 text-gray-900">
				@if($revisions->count() > 0)
					<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
						@foreach($revisions as $rev)
							<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
								<div class="flex items-start justify-between mb-2">
									<div class="flex-1">
										<div class="text-xs text-gray-500 mb-1">Mahasiswa</div>
										<div class="font-semibold text-gray-900">
											{{ $rev->mahasiswa->name }}
											@if($rev->mahasiswa->nim)
												<span class="text-gray-500 font-normal text-sm">({{ $rev->mahasiswa->nim }})</span>
											@endif
										</div>
									</div>
									<div class="flex flex-col items-end gap-1">
										@if($rev->status === 'belum_diperbaiki')
											<form action="{{ route('revisions.update-status', $rev) }}" method="POST" class="inline-block">
												@csrf
												@method('PATCH')
												<button type="submit" 
													onclick="return confirm('Apakah Anda yakin ingin menandai revisi ini sebagai sudah diperbaiki?')"
													class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-50 transition shadow-sm">
													<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
													</svg>
													Tandai Selesai
												</button>
											</form>
											<span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800 mt-1">
												{{ ucfirst(str_replace('_',' ', $rev->status)) }}
											</span>
										@else
											<span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800">
												{{ ucfirst(str_replace('_',' ', $rev->status)) }}
											</span>
										@endif
									</div>
								</div>
								<div class="mt-3 space-y-1">
									<div class="text-sm">
										<span class="text-gray-500">Tahap:</span>
										<span class="px-2 py-0.5 text-xs font-medium rounded
											{{ $rev->tahap === 'proposal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
											{{ ucfirst(str_replace('_', ' ', $rev->tahap)) }}
										</span>
									</div>
									<div class="text-sm text-gray-600">
										Tanggal: {{ $rev->tanggal_revisi->format('d M Y') }}
									</div>
								</div>
								<div class="mt-4 pt-3 border-t border-gray-200">
									<a href="{{ route('revisions.show', $rev) }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
										Lihat Detail
										<svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
										</svg>
									</a>
								</div>
							</div>
						@endforeach
					</div>
					@if($revisions->hasPages())
						<div class="mt-6">
							{{ $revisions->links() }}
						</div>
					@endif
				@else
					<div class="text-center py-12">
						<svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
						</svg>
						<p class="text-gray-500 text-lg mb-2">Belum ada revisi</p>
						<p class="text-gray-400 text-sm mb-4">Mulai dengan membuat revisi baru untuk mahasiswa bimbingan Anda</p>
						<a href="{{ route('revisions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
							<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
							</svg>
							Tambah Revisi Pertama
						</a>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection



