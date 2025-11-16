# SIREDA - Sistem Informasi Revisi Tugas Akhir

Sistem Informasi Revisi Tugas Akhir (SIREDA) adalah aplikasi web berbasis Laravel 12 untuk mengelola proses revisi tugas akhir antara mahasiswa dan dosen pembimbing. Aplikasi ini memungkinkan dosen untuk memberikan revisi kepada mahasiswa dan mahasiswa dapat melacak semua revisi yang telah diterima.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Requirements](#-requirements)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Cara Menggunakan](#-cara-menggunakan)
- [Struktur Proyek](#-struktur-proyek)
- [API Documentation](#-api-documentation)
- [Troubleshooting](#-troubleshooting)

## ✨ Fitur Utama

### 👨‍💼 Admin
- **Manajemen User**: CRUD lengkap untuk mahasiswa dan dosen
- **Import User dari Excel**: Import data user dalam jumlah besar menggunakan file Excel (.xlsx)
- **Export User ke Excel**: Export data user ke format Excel
- **Dashboard Admin**: Overview semua user dan statistik revisi

### 👨‍🏫 Dosen
- **Dashboard Dosen**: Melihat semua mahasiswa bimbingan dan revisi yang telah dibuat
- **Membuat Revisi**: Membuat revisi untuk mahasiswa (termasuk yang bukan bimbingannya)
- **Edit/Hapus Revisi**: Mengelola revisi yang telah dibuat
- **Export PDF Revisi**: Export revisi individual ke PDF dengan tanda tangan
- **Filter Mahasiswa**: Hanya menampilkan mahasiswa yang bukan bimbingan dosen tersebut

### 👨‍🎓 Mahasiswa
- **Dashboard Mahasiswa**: Melihat semua revisi dari semua dosen
- **Rekap Revisi per Dosen**: Statistik revisi dikelompokkan per dosen
- **Export PDF**: 
  - Export rekap revisi (statistik per dosen)
  - Export semua revisi lengkap (detail semua revisi)
- **Share Link**: Link unik untuk berbagi revisi

### 🔐 Authentication & Authorization
- Sistem login/register dengan Laravel Breeze
- Role-based access control (Admin, Dosen, Mahasiswa)
- Password reset dan email verification

## 📦 Requirements

- **PHP**: >= 8.2
- **Composer**: Latest version
- **Node.js**: >= 18.x
- **Database**: SQLite (default) atau MySQL/PostgreSQL
- **Web Server**: Apache/Nginx atau PHP built-in server

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd sireda
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=sqlite
# Atau untuk MySQL/PostgreSQL:
# DB_CONNECTION=
# DB_HOST=
# DB_PORT=
# DB_DATABASE=
# DB_USERNAME=
# DB_PASSWORD=
```

Untuk SQLite, pastikan file `database/database.sqlite` ada:
```bash
touch database/database.sqlite
```

### 5. Jalankan Migration

```bash
php artisan migrate
```

### 6. Seed Database (Optional)

```bash
php artisan db:seed
```

### 7. Build Assets

```bash
npm run build
# Atau untuk development:
npm run dev
```

### 8. Jalankan Server

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://127.0.0.1:8000`

## ⚙️ Konfigurasi

### Environment Variables

File `.env` berisi konfigurasi penting:

```env
APP_NAME=SIREDA
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Jakarta
APP_URL=http://localhost

# Database Configuration
DB_CONNECTION=sqlite

# Mail Configuration (untuk notifikasi)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="${APP_NAME}"
```

### Membuat User Admin

Gunakan artisan command atau seeder:

```bash

```

Atau buat manual melalui tinker:

## 📖 Cara Menggunakan

### Login

1. Buka `http://127.0.0.1:8000/login`
2. Masukkan email dan password
3. Sistem akan redirect ke dashboard sesuai role

### Admin - Import User dari Excel

1. Login sebagai admin
2. Buka menu **Users** → **Import**
3. Download template Excel jika perlu
4. Isi template dengan data user:
   - **nama_lengkap** (wajib): Nama lengkap user
   - **email** (wajib): Email yang unik
   - **password** (opsional): Password, jika kosong akan di-generate otomatis
   - **role** (wajib): `mahasiswa` atau `dosen`
   - **nim** (opsional): Nomor Induk Mahasiswa
   - **judul_tugas_akhir** (opsional): Judul tugas akhir
   - **dosen_pembimbing** (opsional): Nama/email dosen pembimbing (untuk mahasiswa)
5. Upload file Excel
6. Sistem akan menampilkan hasil import dan error jika ada

### Dosen - Membuat Revisi

1. Login sebagai dosen
2. Buka menu **Revisions** → **Create**
3. Pilih mahasiswa (akan menampilkan mahasiswa yang bukan bimbingannya)
4. Isi data revisi:
   - **Tahap**: Proposal atau Sidang Akhir
   - **Tanggal Revisi**: Tanggal revisi
   - **Isi Revisi**: Detail revisi yang perlu diperbaiki
   - **Status**: Belum diperbaiki atau Sudah diperbaiki
   - **Upload Bukti** (opsional): File PDF bukti revisi
5. Klik **Simpan Revisi**
6. Mahasiswa akan menerima notifikasi

### Mahasiswa - Melihat Revisi dan Export PDF

1. Login sebagai mahasiswa
2. Buka **Dashboard Mahasiswa**
3. Anda akan melihat:
   - **Rekap Revisi dari Semua Dosen**: Statistik per dosen
   - **Daftar Revisi Detail**: Semua revisi lengkap
4. Klik tombol **Export Semua Revisi PDF** untuk export semua revisi ke PDF
5. PDF akan berisi semua revisi dari semua dosen dengan format rapi

## 📁 Struktur Proyek

```
sireda/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php    # Controller dashboard per role
│   │   │   ├── RevisiController.php       # Controller CRUD revisi
│   │   │   ├── UserController.php         # Controller CRUD user + import/export
│   │   │   └── ProfileController.php      # Controller profile user
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php         # Middleware untuk role-based access
│   │   └── Requests/
│   ├── Imports/
│   │   └── UserImport.php                 # Class untuk import Excel user
│   ├── Models/
│   │   ├── User.php                       # Model User
│   │   └── Revision.php                   # Model Revision
│   └── Notifications/
│       └── RevisionCreatedNotification.php # Notifikasi revisi baru
├── database/
│   ├── migrations/                        # Database migrations
│   ├── seeders/                          # Database seeders
│   └── database.sqlite                   # SQLite database
├── resources/
│   ├── views/
│   │   ├── auth/                         # Views authentication
│   │   ├── dashboard/                    # Views dashboard per role
│   │   ├── revisions/                    # Views CRUD revisi
│   │   ├── users/                        # Views CRUD user + import
│   │   └── layouts/                      # Layout templates
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php                           # Web routes
│   └── auth.php                          # Authentication routes
├── public/
│   ├── templates/                        # Template Excel untuk import
│   └── storage/                          # File uploads
└── storage/
    └── logs/                             # Application logs
```

## 🔌 API Documentation

### Routes

#### Public Routes
- `GET /` - Welcome page
- `GET /login` - Login form
- `POST /login` - Login process
- `GET /register` - Registration form
- `POST /register` - Registration process
- `GET /share/{token}` - Public share link untuk revisi

#### Authenticated Routes

**Dashboard:**
- `GET /dashboard` - Redirect ke dashboard sesuai role
- `GET /dashboard-admin` - Dashboard admin (role: admin)
- `GET /dashboard-dosen` - Dashboard dosen (role: dosen)
- `GET /dashboard-mahasiswa` - Dashboard mahasiswa (role: mahasiswa)

**User Management (Admin only):**
- `GET /users` - List semua users
- `GET /users/create` - Form create user
- `POST /users` - Store user baru
- `GET /users/{id}/edit` - Form edit user
- `PUT /users/{id}` - Update user
- `DELETE /users/{id}` - Delete user
- `GET /users/import` - Form import Excel
- `POST /users/import` - Proses import Excel
- `GET /users/export/excel` - Export users ke Excel

**Revision Management (Dosen only):**
- `GET /revisions` - List revisi yang dibuat dosen
- `GET /revisions/create` - Form create revisi
- `POST /revisions` - Store revisi baru
- `GET /revisions/{id}` - Detail revisi
- `GET /revisions/{id}/edit` - Form edit revisi
- `PUT /revisions/{id}` - Update revisi
- `DELETE /revisions/{id}` - Delete revisi
- `GET /revisions/{id}/pdf` - Export revisi ke PDF

**Export PDF (Mahasiswa only):**
- `GET /dashboard-mahasiswa/export-rekap-pdf` - Export rekap revisi
- `GET /dashboard-mahasiswa/export-all-revisions-pdf` - Export semua revisi lengkap

**Profile:**
- `GET /profile` - Edit profile
- `PATCH /profile` - Update profile
- `DELETE /profile` - Delete account

## 🗄️ Database Schema

### Users Table
- `id` - Primary key
- `name` - Nama lengkap
- `email` - Email (unique)
- `password` - Password (hashed)
- `role` - Role: `admin`, `dosen`, atau `mahasiswa`
- `nim` - Nomor Induk Mahasiswa (nullable)
- `judul_tugas_akhir` - Judul tugas akhir (nullable)
- `dosen_pembimbing_id` - Foreign key ke users.id (nullable)
- `signature_path` - Path tanda tangan dosen (nullable)
- `email_verified_at` - Timestamp verifikasi email
- `remember_token` - Remember token
- `created_at`, `updated_at` - Timestamps

### Revisions Table
- `id` - Primary key
- `mahasiswa_id` - Foreign key ke users.id
- `dosen_id` - Foreign key ke users.id
- `tahap` - Tahap: `proposal` atau `sidang_akhir`
- `tanggal_revisi` - Tanggal revisi
- `isi_revisi` - Isi revisi (text)
- `status` - Status: `belum_diperbaiki` atau `sudah_diperbaiki`
- `token` - Token unik untuk share link
- `bukti_file_path` - Path file bukti PDF (nullable)
- `created_at`, `updated_at` - Timestamps

### Relationships
- `User` has many `Revisions` (as mahasiswa)
- `User` has many `Revisions` (as dosen)
- `User` belongs to `User` (dosen_pembimbing)
- `User` has many `User` (mahasiswa_bimbingan)

## 🔍 Troubleshooting

### Import Excel Tidak Berfungsi

**Problem**: File Excel tidak bisa diimport atau tidak ada data yang masuk.

**Solutions**:
1. Pastikan format header Excel tepat (case-sensitive):
   - `nama_lengkap` (bukan `nama` atau `Nama Lengkap`)
   - `email`
   - `password`
   - `role`
   - `nim`
   - `judul_tugas_akhir`
   - `dosen_pembimbing`

2. Cek log file: `storage/logs/laravel.log`
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. Pastikan `APP_DEBUG=true` di `.env` untuk melihat error detail

4. Pastikan kolom wajib terisi:
   - `nama_lengkap` (wajib)
   - `email` (wajib, unik)
   - `role` (wajib: `mahasiswa` atau `dosen`)

5. Pastikan email belum terdaftar sebelumnya

### Login Lambat atau Loading Terus

**Problem**: Halaman login loading terus atau proses login lambat.

**Solutions**:
1. Cek koneksi database
2. Cek log untuk error:
   ```bash
   tail -f storage/logs/laravel.log
   ```
3. Pastikan session driver sudah dikonfigurasi di `.env`:
   ```env
   SESSION_DRIVER=file
   ```
4. Clear cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

### PDF Export Error

**Problem**: Error saat export PDF.

**Solutions**:
1. Pastikan library DomPDF terinstall:
   ```bash
   composer require barryvdh/laravel-dompdf
   ```
2. Pastikan folder storage writable:
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```
3. Cek log untuk error detail

### Database Error

**Problem**: Error terkait database.

**Solutions**:
1. Jalankan migration ulang:
   ```bash
   php artisan migrate:fresh
   ```
2. Pastikan file `database/database.sqlite` ada dan writable
3. Untuk MySQL/PostgreSQL, pastikan database sudah dibuat dan kredensial benar di `.env`

## 🧪 Testing

```bash
# Run semua tests
php artisan test

# Run tests dengan coverage
php artisan test --coverage

# Run specific test file
php artisan test --filter UserImportTest
```

## 📝 Logging

Aplikasi menggunakan Laravel Logging untuk tracking aktivitas:

- **User Import**: `storage/logs/laravel.log` - Log proses import Excel
- **Dashboard**: Log akses dashboard dan export PDF
- **Revisions**: Log CRUD operasi revisi
- **Authentication**: Log login/logout

Untuk melihat log real-time:
```bash
tail -f storage/logs/laravel.log
```

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

MIT License - lihat file LICENSE untuk detail lengkap.

## 👥 Authors

- Developer Team SIREDA

## 🙏 Acknowledgments

- Laravel Framework
- Laravel Breeze untuk authentication
- Maatwebsite Excel untuk import/export Excel
- DomPDF untuk export PDF
- Tailwind CSS untuk styling

## 📞 Support

Jika ada pertanyaan atau masalah, silakan:
1. Cek dokumentasi ini
2. Cek log file: `storage/logs/laravel.log`
3. Buat issue di repository

---

**Versi**: 1.0.0  
**Last Updated**: November 2025
