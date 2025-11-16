@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Edit User') }}
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" name="password" minlength="8"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Minimal 8 karakter</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            onchange="toggleMahasiswaFields()">
                            <option value="mahasiswa" {{ old('role', $user->role) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="dosen" {{ old('role', $user->role) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="mahasiswaFields" style="display: {{ old('role', $user->role) === 'mahasiswa' ? 'block' : 'none' }};">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                            <input type="text" name="nim" value="{{ old('nim', $user->nim) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            @error('nim')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Tugas Akhir</label>
                            <textarea name="judul_tugas_akhir" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('judul_tugas_akhir', $user->judul_tugas_akhir) }}</textarea>
                            @error('judul_tugas_akhir')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dosen Pembimbing</label>
                            <select name="dosen_pembimbing_id" id="dosen_pembimbing_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Dosen Pembimbing</option>
                                @foreach(($dosenList ?? collect()) as $dosen)
                                    <option value="{{ $dosen->id }}" {{ (old('dosen_pembimbing_id', $user->dosen_pembimbing_id) == $dosen->id) ? 'selected' : '' }}>
                                        {{ $dosen->name }} &mdash; {{ $dosen->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('dosen_pembimbing_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-4 flex items-center gap-3">
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold shadow-md">
                            Update
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
// make pembimbing required when role is mahasiswa
document.querySelector('select[name="role"]').addEventListener('change', function() {
    const role = this.value;
    const pembimbing = document.getElementById('dosen_pembimbing_id');
    if (!pembimbing) return;
    if (role === 'mahasiswa') {
        pembimbing.setAttribute('required', 'required');
    } else {
        pembimbing.removeAttribute('required');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    if (roleSelect) {
        roleSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection










