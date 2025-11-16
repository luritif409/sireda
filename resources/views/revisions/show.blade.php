@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-blue-900 leading-tight">
    {{ __('Detail Revisi') }}
</h2>
@endsection

@section('content')
<div class="py-6">
	<div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
		<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
			<div class="p-6 space-y-4">
				<div class="border-b border-gray-200 pb-4">
					<h2 class="text-2xl font-semibold text-blue-900 mb-4">Detail Revisi</h2>
					
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<div class="text-sm font-medium text-blue-800 mb-1">Mahasiswa</div>
							<div class="text-base font-semibold text-blue-900">
								{{ $revision->mahasiswa->name }}
								@if($revision->mahasiswa->nim)
									<span class="text-blue-800 font-normal">({{ $revision->mahasiswa->nim }})</span>
								@endif
							</div>
						</div>
						
						<div>
							<div class="text-sm font-medium text-blue-800 mb-1">Dosen Pembimbing</div>
							<div class="text-base font-semibold text-blue-900">{{ $revision->dosen->name }}</div>
						</div>
						
						<div>
							<div class="text-sm font-medium text-blue-800 mb-1">Tahap</div>
							<div>
								<span class="px-3 py-1 text-sm font-medium rounded
									{{ $revision->tahap === 'proposal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
									{{ ucfirst(str_replace('_', ' ', $revision->tahap)) }}
								</span>
							</div>
						</div>
						
						<div>
							<div class="text-sm font-medium text-blue-800 mb-1">Tanggal Revisi</div>
							<div class="text-base font-semibold text-blue-900">{{ $revision->tanggal_revisi->format('d M Y') }}</div>
						</div>
						
						<div>
							<div class="text-sm font-medium text-blue-800 mb-1">Status</div>
							<div>
								<span class="px-3 py-1 text-sm font-medium rounded
									{{ $revision->status === 'belum_diperbaiki' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
									{{ ucfirst(str_replace('_', ' ', $revision->status)) }}
								</span>
							</div>
						</div>
						
						@if($revision->bukti_file_path)
						<div>
							<div class="text-sm font-medium text-blue-800 mb-1">Bukti File</div>
							<div>
								<a href="{{ Storage::url($revision->bukti_file_path) }}" class="text-blue-800 hover:text-blue-900 font-semibold underline inline-flex items-center" target="_blank">
									<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
									</svg>
									Lihat PDF
								</a>
							</div>
						</div>
						@endif
					</div>
				</div>

				<div class="pt-4">
					<div class="text-sm font-medium text-blue-800 mb-2">Isi Revisi</div>
					<div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
						<div class="text-base text-blue-900 whitespace-pre-wrap leading-relaxed font-medium">{{ $revision->isi_revisi }}</div>
					</div>
				</div>

				@if($revision->token)
				<div class="pt-4 border-t border-gray-200">
					<div class="text-sm font-medium text-blue-800 mb-2">Share Link</div>
					<div class="flex items-center gap-2">
						<input type="text" readonly value="{{ route('revisions.share', $revision->token) }}" 
							class="flex-1 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-sm text-blue-900 font-medium" 
							id="shareLink">
						<button onclick="copyShareLink()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-semibold shadow-md hover:shadow-lg transition">
							Copy
						</button>
					</div>
				</div>
				@endif

				<div class="pt-6 flex flex-wrap items-center gap-3 border-t border-gray-200">
					<a href="{{ route('revisions.edit', $revision) }}" class="inline-flex items-center px-5 py-2.5 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 text-sm font-semibold shadow-md hover:shadow-lg transition">
						<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
						</svg>
						Edit
					</a>
					<form method="POST" action="{{ route('revisions.destroy', $revision) }}" onsubmit="return confirm('Hapus revisi ini?')" class="inline">
						@csrf
						@method('DELETE')
						<button type="submit" class="inline-flex items-center px-5 py-2.5 bg-white border-2 border-red-600 text-red-600 rounded-lg hover:bg-red-50 text-sm font-semibold shadow-md hover:shadow-lg transition">
							<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
							</svg>
							Hapus
						</button>
					</form>
					<a href="{{ route('revisions.pdf', $revision) }}" class="inline-flex items-center px-5 py-2.5 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 text-sm font-semibold shadow-md hover:shadow-lg transition">
						<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
						</svg>
						Export ke PDF
					</a>
					<a href="{{ route('revisions.share', $revision->token) }}" class="inline-flex items-center px-5 py-2.5 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 text-sm font-semibold shadow-md hover:shadow-lg transition" target="_blank">
						<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
						</svg>
						Share Link
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

@if($revision->token)
<script>
function copyShareLink() {
	const input = document.getElementById('shareLink');
	input.select();
	input.setSelectionRange(0, 99999);
	document.execCommand('copy');
	alert('Link berhasil disalin!');
}
</script>
@endif
@endsection



