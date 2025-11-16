@extends('layouts.app')

@section('header')
<div class="flex items-center justify-between">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Tambah User Baru') }}
    </h2>
    <a href="{{ route('users.index') }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
        ← Kembali ke Daftar User
    </a>
</div>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                @if($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
                    @csrf

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-blue-800">
                            <strong>Info:</strong> Pilih role "Mahasiswa" untuk menambahkan mahasiswa yang akan direvisi oleh dosen. 
                            Pilih role "Dosen" untuk menambahkan dosen pembimbing baru.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan nama lengkap">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="contoh@email.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <input type="password" name="password" required minlength="8"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Minimal 8 karakter">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                        <select name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            onchange="toggleMahasiswaFields()">
                            <option value="">Pilih Role</option>
                            <option value="mahasiswa" {{ old('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa (untuk direvisi dosen)</option>
                            <option value="dosen" {{ old('role') === 'dosen' ? 'selected' : '' }}>Dosen (pembimbing)</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="mahasiswaFields" style="display: none;" class="space-y-4 border-t border-gray-200 pt-4">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <p class="text-sm text-green-800">
                                <strong>Data Khusus Mahasiswa:</strong> Isi NIM dan Judul Tugas Akhir untuk mahasiswa yang akan direvisi oleh dosen.
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">NIM</label>
                            <input type="text" name="nim" value="{{ old('nim') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Contoh: 2021001">
                            @error('nim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Nomor Induk Mahasiswa</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Tugas Akhir</label>
                            <textarea name="judul_tugas_akhir" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Masukkan judul tugas akhir mahasiswa...">{{ old('judul_tugas_akhir') }}</textarea>
                            @error('judul_tugas_akhir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Judul tugas akhir yang akan direvisi oleh dosen</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dosen Pembimbing *</label>
                            <select name="dosen_pembimbing_id" id="dosen_pembimbing_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Dosen Pembimbing</option>
                                @foreach(($dosenList ?? collect()) as $dosen)
                                    <option value="{{ $dosen->id }}" {{ old('dosen_pembimbing_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ $dosen->name }} &mdash; {{ $dosen->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_pembimbing_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Pilih dosen pembimbing untuk mahasiswa ini</p>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center gap-3 border-t border-gray-200">
                        <button type="submit" class="px-5 py-2.5 bg-white border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 text-sm font-semibold shadow-md hover:shadow-lg transition">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan User
                        </button>
                        <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-semibold">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMahasiswaFields() {
    const role = document.querySelector('select[name="role"]').value;
    const fields = document.getElementById('mahasiswaFields');
    if (role === 'mahasiswa') {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleMahasiswaFields();
});
</script>

<script>
// Ensure dosen_pembimbing_id is required when role == mahasiswa
document.querySelector('select[name="role"]').addEventListener('change', function() {
    const role = this.value;
    const pembimbing = document.getElementById('dosen_pembimbing_id');
    if (!pembimbing) return;
    if (role === 'mahasiswa') {
        pembimbing.setAttribute('required', 'required');
    } else {
        pembimbing.removeAttribute('required');
        pembimbing.value = '';
    }
});

// Initialize required state on load
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    if (roleSelect) {
        const event = new Event('change');
        roleSelect.dispatchEvent(event);
    }
});
</script>
@endsection

