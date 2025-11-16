@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Mahasiswa Bimbingan') }}
</h2>
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
		<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
			<div class="p-6 text-gray-900">
				@if($mahasiswaList->count() > 0)
					<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
						@foreach($mahasiswaList as $mahasiswa)
							<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
								<div class="flex items-start justify-between mb-3">
									<div class="flex-1">
										<div class="font-semibold text-lg text-gray-900 mb-1">
											{{ $mahasiswa->name }}
										</div>
										@if($mahasiswa->nim)
											<div class="text-sm text-gray-600 mb-2">
												NIM: {{ $mahasiswa->nim }}
											</div>
										@endif
										@if($mahasiswa->email)
											<div class="text-sm text-gray-500 mb-2">
												{{ $mahasiswa->email }}
											</div>
										@endif
									</div>
									<div class="ml-3 flex-shrink-0">
										<div class="bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-sm font-bold">
											{{ $mahasiswa->total_revisi ?? 0 }} Revisi
										</div>
									</div>
								</div>
								
								@if($mahasiswa->judul_tugas_akhir)
									<div class="mt-3 pt-3 border-t border-gray-200">
										<div class="text-xs text-gray-500 mb-1">Judul Tugas Akhir</div>
										<div class="text-sm text-gray-700">
											{{ Str::limit($mahasiswa->judul_tugas_akhir, 100) }}
										</div>
									</div>
								@endif

								@php
									// Gunakan data yang sudah di-preload dari controller (tidak perlu query lagi)
									$revisiList = $mahasiswa->revisions ?? collect();
									$revisiBelumDiperbaiki = $mahasiswa->revisi_belum_diperbaiki ?? 0;
									$revisiSudahDiperbaiki = $mahasiswa->revisi_sudah_diperbaiki ?? 0;
								@endphp

								<div class="mt-4 pt-3 border-t border-gray-200">
									<div class="grid grid-cols-2 gap-3 mb-3">
										<div class="bg-gray-50 rounded-lg p-2 text-center">
											<div class="text-xs text-gray-500 mb-1">Total</div>
											<div class="text-lg font-bold text-gray-900">{{ $mahasiswa->total_revisi ?? 0 }}</div>
										</div>
										@if($revisiBelumDiperbaiki > 0)
											<div class="bg-red-50 rounded-lg p-2 text-center">
												<div class="text-xs text-red-600 mb-1">Belum Diperbaiki</div>
												<div class="text-lg font-bold text-red-600">{{ $revisiBelumDiperbaiki }}</div>
											</div>
										@else
											<div class="bg-green-50 rounded-lg p-2 text-center">
												<div class="text-xs text-green-600 mb-1">Sudah Diperbaiki</div>
												<div class="text-lg font-bold text-green-600">{{ $revisiSudahDiperbaiki }}</div>
											</div>
										@endif
									</div>
									@php
										// Hitung apakah semua revisi sudah selesai
										$selesaiSemua = ($mahasiswa->total_revisi ?? 0) > 0 && ($revisiBelumDiperbaiki ?? 0) === 0;
									@endphp
									<div class="mt-2">
										@if($selesaiSemua)
											<div class="bg-green-50 rounded-lg p-2 text-center border border-green-200">
												<div class="text-xs text-green-600 mb-1">Status</div>
												<div class="text-sm font-bold text-green-700">
													✓ Selesai Semua Revisi
												</div>
											</div>
										@else
											<div class="bg-red-50 rounded-lg p-2 text-center border border-red-200">
												<div class="text-xs text-red-600 mb-1">Status</div>
												<div class="text-sm font-bold text-red-700">
													Masih Ada Revisi Belum Selesai
												</div>
											</div>
										@endif
									</div>
									
									@if($revisiList->count() > 0)
										<div class="mb-3">
											<button onclick="toggleDetail({{ $mahasiswa->id }})" 
												style="border-color: #a4c8f5; color: #a4c8f5;"
												class="w-full inline-flex items-center justify-center px-3 py-2 bg-white border-2 text-xs font-medium rounded-md hover:opacity-80 transition mb-2">
												<svg id="icon-{{ $mahasiswa->id }}" class="w-4 h-4 mr-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
												</svg>
												Detail Revisi
											</button>
											<div id="detail-{{ $mahasiswa->id }}" class="hidden mt-2 space-y-2 max-h-96 overflow-y-auto">
												@foreach($revisiList as $rev)
													<div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
														<div class="flex items-start justify-between mb-2">
															<div class="flex-1">
																<div class="text-xs font-semibold text-gray-700 mb-1">
																	Revisi #{{ $loop->iteration }} - {{ ucfirst(str_replace('_', ' ', $rev->tahap)) }}
																</div>
																<div class="text-xs text-gray-500">
																	Dari: {{ $rev->dosen->name }}
																</div>
															</div>
															<span class="px-2 py-1 text-xs font-medium rounded
																{{ $rev->status === 'belum_diperbaiki' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
																{{ ucfirst(str_replace('_',' ', $rev->status)) }}
															</span>
														</div>
														<div class="text-xs text-gray-600 mb-2">
															{{ Str::limit($rev->isi_revisi, 150) }}
														</div>
														<div class="flex items-center justify-between text-xs text-gray-500 mb-2">
															<span>{{ $rev->tanggal_revisi->format('d M Y') }}</span>
														</div>
														@if($rev->status === 'belum_diperbaiki')
															<form action="{{ route('revisions.update-status', $rev) }}" method="POST" class="mt-2">
																@csrf
																@method('PATCH')
																<button type="submit" 
																	onclick="return confirm('Apakah Anda yakin ingin menandai revisi ini sebagai sudah diperbaiki?')"
																	class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-white border-2 border-blue-600 text-blue-600 text-xs font-medium rounded-md hover:bg-blue-50 transition">
																	<svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																		<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
																	</svg>
																	Tandai Selesai
																</button>
															</form>
														@elseif($rev->status === 'sudah_diperbaiki')
															<div class="mt-2 text-center">
																<span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-800 text-xs font-medium rounded-md">
																	<svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																		<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
																	</svg>
																	Sudah Diperbaiki
																</span>
															</div>
														@endif
													</div>
												@endforeach
											</div>
										</div>
									@endif
								</div>
							</div>
						@endforeach
					</div>
				@else
					<div class="text-center py-12">
						<svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
						</svg>
						<p class="text-gray-500 text-lg mb-2">Belum ada mahasiswa bimbingan</p>
						<p class="text-gray-400 text-sm mb-4">Mahasiswa yang Anda bimbing akan muncul di sini</p>
					</div>
				@endif
			</div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script>
	function toggleDetail(mahasiswaId) {
		const detail = document.getElementById('detail-' + mahasiswaId);
		const icon = document.getElementById('icon-' + mahasiswaId);
		if (detail.classList.contains('hidden')) {
			detail.classList.remove('hidden');
			icon.classList.add('rotate-180');
		} else {
			detail.classList.add('hidden');
			icon.classList.remove('rotate-180');
		}
	}
</script>
@endpush

