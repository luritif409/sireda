@extends('layouts.app')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard Dosen') }}
    </h2>
    <a href="{{ route('revisions.create') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        + Buat Revisi
    </a>
</div>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @php
            $totalRevisions = 0;
            $totalMahasiswa = count($revisions);
            $belumDiperbaiki = 0;
            foreach($revisions as $items) {
                $totalRevisions += $items->count();
                foreach($items as $rev) {
                    if($rev->status === 'belum_diperbaiki') {
                        $belumDiperbaiki++;
                    }
                }
            }
        @endphp

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Mahasiswa</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalMahasiswa }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Revisi</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalRevisions }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Belum Diperbaiki</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $belumDiperbaiki }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mahasiswa List -->
        <div class="space-y-4">
            @forelse($revisions as $mahasiswaId => $items)
                @php
                    $mahasiswa = $items->first()->mahasiswa;
                    $revisiBelum = $items->where('status', 'belum_diperbaiki')->count();
                    $revisiSudah = $items->where('status', 'sudah_diperbaiki')->count();
                @endphp
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $mahasiswa->name }}
                                </h3>
                                @if($mahasiswa->nim)
                                    <p class="text-sm text-gray-500">NIM: {{ $mahasiswa->nim }}</p>
                                @endif
                                @if($mahasiswa->judul_tugas_akhir)
                                    <p class="text-sm text-gray-600 mt-1">{{ $mahasiswa->judul_tugas_akhir }}</p>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                @if($revisiBelum > 0)
                                    <span class="px-3 py-1 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">
                                        {{ $revisiBelum }} Belum
                                    </span>
                                @endif
                                @if($revisiSudah > 0)
                                    <span class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        {{ $revisiSudah }} Selesai
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($items as $rev)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex items-start justify-between mb-2">
                                        <span class="px-2 py-1 text-xs font-medium rounded
                                            {{ $rev->tahap === 'proposal' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $rev->tahap)) }}
                                        </span>
                                        <span class="px-2 py-1 text-xs font-medium rounded
                                            {{ $rev->status === 'belum_diperbaiki' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $rev->status === 'belum_diperbaiki' ? 'Belum' : 'Selesai' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-3">
                                        {{ $rev->tanggal_revisi->format('d M Y') }}
                                    </p>
                                    <p class="text-sm text-gray-700 line-clamp-2 mb-3">
                                        {{ Str::limit($rev->isi_revisi, 80) }}
                                    </p>
                                    <a href="{{ route('revisions.show', $rev) }}" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                        Lihat Detail
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg mb-2">Belum ada revisi</p>
                    <p class="text-gray-400 text-sm mb-4">Mulai dengan membuat revisi baru untuk mahasiswa bimbingan Anda</p>
                    <a href="{{ route('revisions.create') }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 shadow-md hover:shadow-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        + Tambah Revisi Pertama
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection



