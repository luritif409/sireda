<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Revision;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil dosen yang sudah ada
        $dosen = User::where('email', 'dosen@example.com')->first();
        
        if (!$dosen) {
            $this->command->error('Dosen tidak ditemukan. Pastikan seeder AdminUserSeeder sudah dijalankan.');
            return;
        }

        // Buat 3 mahasiswa yang sedang sidang
        $mahasiswa1 = User::firstOrCreate(
            ['email' => 'mahasiswa1@example.com'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '2021001',
                'judul_tugas_akhir' => 'Sistem Informasi Manajemen Perpustakaan Berbasis Web',
            ]
        );

        $mahasiswa2 = User::firstOrCreate(
            ['email' => 'mahasiswa2@example.com'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '2021002',
                'judul_tugas_akhir' => 'Aplikasi E-Commerce untuk UMKM dengan Fitur Chatbot',
            ]
        );

        $mahasiswa3 = User::firstOrCreate(
            ['email' => 'mahasiswa3@example.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '2021003',
                'judul_tugas_akhir' => 'Sistem Monitoring Kualitas Air Berbasis IoT',
            ]
        );

        // Buat sample revisi untuk mahasiswa 1 (Ahmad Fauzi)
        Revision::firstOrCreate(
            [
                'mahasiswa_id' => $mahasiswa1->id,
                'dosen_id' => $dosen->id,
                'tahap' => 'sidang_akhir',
                'tanggal_revisi' => now()->subDays(5),
            ],
            [
                'isi_revisi' => '1. Perbaiki struktur database pada tabel transaksi peminjaman buku. Tambahkan field status_peminjaman dengan enum yang jelas.
2. Pada halaman dashboard, tambahkan grafik statistik peminjaman per bulan.
3. Perbaiki validasi form registrasi anggota, pastikan NIK tidak boleh duplikat.
4. Tambahkan fitur export laporan ke Excel untuk data peminjaman.
5. Perbaiki tampilan mobile responsive pada halaman detail buku.',
                'status' => 'belum_diperbaiki',
                'token' => Str::uuid(),
            ]
        );

        Revision::firstOrCreate(
            [
                'mahasiswa_id' => $mahasiswa1->id,
                'dosen_id' => $dosen->id,
                'tahap' => 'sidang_akhir',
                'tanggal_revisi' => now()->subDays(2),
            ],
            [
                'isi_revisi' => '1. Perbaikan struktur database sudah baik. Lanjutkan dengan implementasi grafik.
2. Tambahkan notifikasi email otomatis untuk pengingat pengembalian buku.
3. Perbaiki error handling pada saat koneksi database gagal.',
                'status' => 'sudah_diperbaiki',
                'token' => Str::uuid(),
            ]
        );

        // Buat sample revisi untuk mahasiswa 2 (Siti Nurhaliza)
        Revision::firstOrCreate(
            [
                'mahasiswa_id' => $mahasiswa2->id,
                'dosen_id' => $dosen->id,
                'tahap' => 'sidang_akhir',
                'tanggal_revisi' => now()->subDays(7),
            ],
            [
                'isi_revisi' => '1. Perbaiki algoritma rekomendasi produk pada halaman beranda. Gunakan collaborative filtering yang lebih akurat.
2. Tambahkan fitur filter produk berdasarkan kategori, harga, dan rating.
3. Perbaiki integrasi payment gateway, pastikan callback berfungsi dengan baik.
4. Chatbot perlu dilatih dengan dataset yang lebih lengkap untuk meningkatkan akurasi respons.
5. Tambahkan fitur wishlist untuk produk yang ingin dibeli nanti.',
                'status' => 'belum_diperbaiki',
                'token' => Str::uuid(),
            ]
        );

        Revision::firstOrCreate(
            [
                'mahasiswa_id' => $mahasiswa2->id,
                'dosen_id' => $dosen->id,
                'tahap' => 'sidang_akhir',
                'tanggal_revisi' => now()->subDays(3),
            ],
            [
                'isi_revisi' => '1. Algoritma rekomendasi sudah diperbaiki dengan baik.
2. Filter produk sudah berfungsi dengan baik.
3. Perbaiki tampilan mobile pada halaman checkout agar lebih user-friendly.
4. Tambahkan fitur tracking order untuk pelanggan.',
                'status' => 'belum_diperbaiki',
                'token' => Str::uuid(),
            ]
        );

        // Buat sample revisi untuk mahasiswa 3 (Budi Santoso)
        Revision::firstOrCreate(
            [
                'mahasiswa_id' => $mahasiswa3->id,
                'dosen_id' => $dosen->id,
                'tahap' => 'sidang_akhir',
                'tanggal_revisi' => now()->subDays(6),
            ],
            [
                'isi_revisi' => '1. Perbaiki kalibrasi sensor pH dan suhu. Pastikan akurasi pembacaan sesuai standar.
2. Tambahkan alert otomatis ketika kualitas air di bawah ambang batas normal.
3. Perbaiki tampilan dashboard monitoring, tambahkan grafik real-time.
4. Implementasikan sistem backup data otomatis setiap hari.
5. Tambahkan fitur export data monitoring ke PDF untuk laporan bulanan.',
                'status' => 'belum_diperbaiki',
                'token' => Str::uuid(),
            ]
        );

        Revision::firstOrCreate(
            [
                'mahasiswa_id' => $mahasiswa3->id,
                'dosen_id' => $dosen->id,
                'tahap' => 'sidang_akhir',
                'tanggal_revisi' => now()->subDays(1),
            ],
            [
                'isi_revisi' => '1. Kalibrasi sensor sudah diperbaiki dan diuji dengan baik.
2. Sistem alert sudah berfungsi dengan baik, notifikasi terkirim via email dan SMS.
3. Dashboard monitoring sudah diperbaiki dengan grafik real-time yang smooth.
4. Perbaiki performa aplikasi, optimasi query database untuk data historis.',
                'status' => 'sudah_diperbaiki',
                'token' => Str::uuid(),
            ]
        );

        $this->command->info('Sample data berhasil dibuat!');
        $this->command->info('Mahasiswa 1: ' . $mahasiswa1->email . ' / password');
        $this->command->info('Mahasiswa 2: ' . $mahasiswa2->email . ' / password');
        $this->command->info('Mahasiswa 3: ' . $mahasiswa3->email . ' / password');
        $this->command->info('Total revisi dibuat: ' . Revision::count());
    }
}










