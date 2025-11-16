@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Import User dari Excel') }}
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <strong>Error:</strong> {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        ✓ {{ session('success') }}
                    </div>
                @endif

                @if(session('import_errors'))
                    <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                        <p class="font-semibold mb-2">⚠ Baris yang tidak diimport:</p>
                        <ul class="list-disc list-inside text-sm space-y-1 max-h-64 overflow-y-auto">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Format File Excel</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-blue-900 mb-3">
                            File harus berformat <strong>Excel (.xlsx atau .xls)</strong> dengan kolom berikut di baris pertama (header):
                        </p>
                        <div class="bg-white rounded p-3 font-mono text-sm">
                            <code>nama_lengkap | email | password | role | nim | judul_tugas_akhir | dosen_pembimbing</code>
                        </div>
                    </div>

                    <div class="overflow-x-auto mb-4">
                        <table class="min-w-full text-sm border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 border border-gray-300 text-left font-semibold">Kolom</th>
                                    <th class="px-3 py-2 border border-gray-300 text-left font-semibold">Tipe</th>
                                    <th class="px-3 py-2 border border-gray-300 text-left font-semibold">Contoh</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-3 py-2 border border-gray-300"><strong>nama_lengkap</strong></td>
                                    <td class="px-3 py-2 border border-gray-300">Wajib</td>
                                    <td class="px-3 py-2 border border-gray-300">Ahmad Fauzi</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-3 py-2 border border-gray-300"><strong>email</strong></td>
                                    <td class="px-3 py-2 border border-gray-300">Wajib (unik)</td>
                                    <td class="px-3 py-2 border border-gray-300">ahmad@example.com</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 border border-gray-300"><strong>password</strong></td>
                                    <td class="px-3 py-2 border border-gray-300">Opsional</td>
                                    <td class="px-3 py-2 border border-gray-300">password123</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-3 py-2 border border-gray-300"><strong>role</strong></td>
                                    <td class="px-3 py-2 border border-gray-300">Wajib</td>
                                    <td class="px-3 py-2 border border-gray-300">mahasiswa / dosen</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 border border-gray-300"><strong>nim</strong></td>
                                    <td class="px-3 py-2 border border-gray-300">Opsional</td>
                                    <td class="px-3 py-2 border border-gray-300">2022001</td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-3 py-2 border border-gray-300"><strong>judul_tugas_akhir</strong></td>
                                    <td class="px-3 py-2 border border-gray-300">Opsional</td>
                                    <td class="px-3 py-2 border border-gray-300">Sistem Manajemen...</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 border border-gray-300"><strong>dosen_pembimbing</strong></td>
                                    <td class="px-3 py-2 border border-gray-300">Opsional*</td>
                                    <td class="px-3 py-2 border border-gray-300">Dr. Budi Santoso</td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="text-xs text-gray-600 mt-2">
                            * Untuk mahasiswa: isi nama atau email dosen yang sudah terdaftar. Jika kosong, baris akan skip.
                        </p>
                    </div>

                    <div class="space-y-2 text-sm text-gray-700">
                        <p><strong>Catatan penting:</strong></p>
                        <ul class="list-disc list-inside space-y-1 ml-2">
                            <li>Kolom <strong>nama_lengkap</strong>, <strong>email</strong>, dan <strong>role</strong> wajib diisi</li>
                            <li>Email harus unik (tidak boleh sudah terdaftar)</li>
                            <li>Role hanya: <strong>mahasiswa</strong> atau <strong>dosen</strong> (huruf kecil)</li>
                            <li>Jika password kosong, sistem akan generate otomatis</li>
                            <li>Dosen pembimbing harus cocok dengan nama/email dosen yang sudah ada di sistem</li>
                            <li>Kolom NIM dan Judul bisa kosong untuk dosen</li>
                            <li>Simpan file sebagai <strong>.xlsx</strong> (Excel 2007+) atau <strong>.xls</strong> (Excel 97-2003)</li>
                        </ul>
                    </div>
                </div>

                <hr class="my-6" />

                <form method="POST" action="{{ route('users.import') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih File Excel
                        </label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">
                            Format: XLSX, XLS, CSV (Maks: 5MB)
                        </p>
                        @error('file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 text-sm font-semibold shadow-md hover:shadow-lg transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Import User
                        </button>
                        <a href="{{ asset('templates/ImportUserTemplate.xlsx') }}" download class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 text-sm font-medium transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download Template
                        </a>
                        <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

