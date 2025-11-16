# Dokumentasi SIREDA

## 📚 Daftar Isi

1. [Panduan Instalasi Lengkap](#panduan-instalasi-lengkap)
2. [Panduan Penggunaan](#panduan-penggunaan)
3. [Format Import Excel](#format-import-excel)
4. [API Endpoints](#api-endpoints)
5. [Database Schema](#database-schema)
6. [Troubleshooting Detail](#troubleshooting-detail)

## 🚀 Panduan Instalasi Lengkap

### Step-by-Step Installation

#### 1. Prerequisites

Pastikan sistem Anda memiliki:
- PHP 8.2 atau lebih tinggi
- Composer (PHP package manager)
- Node.js 18.x atau lebih tinggi
- NPM atau Yarn
- Git

**Cek versi:**
```bash
php -v        # Harus >= 8.2
composer -v   # Latest version
node -v       # Harus >= 18.x
npm -v        # Latest version
```

#### 2. Clone Repository

```bash
git clone <repository-url>
cd sireda
```

#### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

#### 4. Setup Environment

```bash
# Copy file .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 5. Konfigurasi Database

**Opsi A: SQLite (Default - Paling Mudah)**

Pastikan file database.sqlite ada:
```bash
touch database/database.sqlite
```

File `.env` sudah benar untuk SQLite:
```env
DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite (otomatis)
```

**Opsi B: MySQL**

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sireda
DB_USERNAME=root
DB_PASSWORD=your_password
```

Buat database:
```sql
CREATE DATABASE sireda;
```

**Opsi C: PostgreSQL**

Edit `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sireda
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

Buat database:
```sql
CREATE DATABASE sireda;
```

#### 6. Jalankan Migration

```bash
php artisan migrate
```

#### 7. Seed Database (Optional - untuk data sample)

```bash
php artisan db:seed
```

#### 8. Setup Storage Link

```bash
php artisan storage:link
```

#### 9. Build Assets

**Development:**
```bash
npm run dev
```

**Production:**
```bash
npm run build
```

#### 10. Set Permissions

```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Windows (gunakan Git Bash atau PowerShell as Admin)
# Biasanya tidak perlu karena Windows sudah handle permissions
```

#### 11. Jalankan Server

```bash
php artisan serve
```

Aplikasi akan berjalan di: `http://127.0.0.1:8000`

## 📖 Panduan Penggunaan

### Admin - Manajemen User

#### Menambah User Manual

1. Login sebagai admin
2. Buka menu **Users** → **Create**
3. Isi form:
   - Nama lengkap
   - Email (harus unik)
   - Password (minimal 8 karakter)
   - Role: Mahasiswa atau Dosen
   - NIM (untuk mahasiswa)
   - Judul Tugas Akhir (opsional)
   - Dosen Pembimbing (untuk mahasiswa)
4. Klik **Simpan**

#### Import User dari Excel

1. **Download Template:**
   - Buka menu **Users** → **Import**
   - Klik **Download Template**
   - Buka file `ImportUserTemplate.xlsx`

2. **Isi Template:**
   - Baris pertama adalah header (jangan dihapus)
   - Baris kedua dan seterusnya adalah data user
   - Pastikan header tepat (case-sensitive):
     - `nama_lengkap`
     - `email`
     - `password`
     - `role`
     - `nim`
     - `judul_tugas_akhir`
     - `dosen_pembimbing`

3. **Upload File:**
   - Klik **Pilih File Excel**
   - Pilih file yang sudah diisi
   - Klik **Import User**

4. **Cek Hasil:**
   - Pesan sukses akan muncul jika berhasil
   - Jika ada error, akan ditampilkan detail baris yang gagal
   - Cek log: `storage/logs/laravel.log` untuk detail lengkap

#### Export User ke Excel

1. Login sebagai admin
2. Buka menu **Users**
3. Gunakan filter jika perlu (cari nama/email/NIM atau filter role)
4. Klik **Export Excel**
5. File akan terdownload otomatis

### Dosen - Manajemen Revisi

#### Membuat Revisi

1. Login sebagai dosen
2. Buka menu **Revisions** → **Create**
3. Pilih mahasiswa dari dropdown
   - Dropdown hanya menampilkan mahasiswa yang **bukan** bimbingan dosen tersebut
4. Isi form revisi:
   - **Tahap**: Proposal atau Sidang Akhir
   - **Tanggal Revisi**: Pilih tanggal
   - **Isi Revisi**: Tuliskan detail revisi yang perlu diperbaiki
   - **Status**: 
     - Belum diperbaiki (default untuk revisi baru)
     - Sudah diperbaiki (jika mahasiswa sudah memperbaiki)
   - **Upload Bukti** (opsional): Upload file PDF sebagai bukti
5. Klik **Simpan Revisi**
6. Mahasiswa akan menerima notifikasi

#### Edit/Hapus Revisi

1. Buka menu **Revisions**
2. Klik **Edit** pada revisi yang ingin diubah
3. Ubah data yang diperlukan
4. Klik **Update**
5. Atau klik **Delete** untuk menghapus revisi

#### Export Revisi ke PDF

1. Buka detail revisi (klik revisi dari list)
2. Klik **Export PDF**
3. PDF akan terdownload dengan format formal termasuk tanda tangan dosen (jika ada)

### Mahasiswa - Melihat Revisi

#### Dashboard Mahasiswa

1. Login sebagai mahasiswa
2. Anda akan melihat:
   - **Rekap Revisi dari Semua Dosen**: Statistik per dosen dengan:
     - Total revisi
     - Belum diperbaiki
     - Sudah diperbaiki
     - Progress bar
   - **Daftar Revisi Detail**: Semua revisi lengkap dengan:
     - Nama dosen
     - Tahap revisi
     - Tanggal revisi
     - Isi revisi
     - Status
     - Link untuk share dan lihat bukti

#### Export PDF Revisi

**Export Semua Revisi:**
1. Di dashboard mahasiswa
2. Klik tombol **Export Semua Revisi PDF** (merah di pojok kanan atas)
3. PDF akan terdownload dengan:
   - Judul dinamis berdasarkan tahap (Proposal atau Tugas Akhir)
   - Informasi mahasiswa
   - Semua revisi dari semua dosen lengkap dengan detail

**Format PDF:**
- Header: Judul laporan
- Informasi mahasiswa (nama, email, NIM, judul tugas akhir)
- Daftar revisi dikelompokkan per dosen
- Setiap revisi berisi:
  - Nomor revisi (Revisi ke-1, ke-2, dst.)
  - Tahap, Tanggal Revisi, Tanggal Dibuat
  - Isi revisi lengkap
  - Info bukti (jika ada)

#### Share Link Revisi

1. Di dashboard, klik **Buka Share Link** pada revisi tertentu
2. Link dapat dibagikan ke orang lain
3. Link tidak memerlukan login untuk melihat revisi

## 📊 Format Import Excel

### Header Excel (Baris 1)

Header harus tepat seperti berikut (case-sensitive):

```
nama_lengkap | email | password | role | nim | judul_tugas_akhir | dosen_pembimbing
```

### Contoh Data (Baris 2 dan seterusnya)

```
Ahmad Fauzi | ahmad.fauzi@example.com | password123 | mahasiswa | 2022001 | Sistem Informasi Manajemen | Dr. Budi Santoso
Dr. Budi Santoso | budi.santoso@example.com | password123 | dosen | | |
Siti Nurhaliza | siti.nur@example.com | password123 | mahasiswa | 2022002 | Aplikasi Mobile Learning | Dr. Budi Santoso
```

### Aturan Import

**Kolom Wajib:**
- `nama_lengkap`: Nama lengkap user (minimal 1 karakter)
- `email`: Email yang valid dan unik (format email benar)
- `role`: Harus `mahasiswa` atau `dosen` (huruf kecil)

**Kolom Opsional:**
- `password`: Jika kosong, akan di-generate otomatis (12 karakter random)
- `nim`: Nomor Induk Mahasiswa (hanya untuk mahasiswa)
- `judul_tugas_akhir`: Judul tugas akhir (opsional)
- `dosen_pembimbing`: Nama atau email dosen pembimbing (untuk mahasiswa)

**Validasi:**
- Email harus unik (tidak boleh sudah terdaftar)
- Email harus format valid
- Role harus `mahasiswa` atau `dosen`
- Dosen pembimbing harus ada di database (jika diisi)
- Dosen pembimbing harus memiliki role `dosen`

**Error Handling:**
- Baris yang gagal akan ditampilkan dengan detail error
- Baris yang valid akan diimport
- Jika semua baris gagal, tidak ada data yang masuk

## 🔌 API Endpoints

### Authentication Endpoints

```
POST   /login              - Login user
POST   /logout             - Logout user
GET    /register           - Registration form
POST   /register           - Register new user
GET    /forgot-password    - Forgot password form
POST   /forgot-password    - Send password reset link
GET    /reset-password/{token} - Reset password form
POST   /reset-password     - Reset password
```

### Dashboard Endpoints

```
GET    /dashboard          - Redirect ke dashboard sesuai role
GET    /dashboard-admin    - Dashboard admin (role: admin)
GET    /dashboard-dosen    - Dashboard dosen (role: dosen)
GET    /dashboard-mahasiswa - Dashboard mahasiswa (role: mahasiswa)
```

### User Management Endpoints (Admin Only)

```
GET    /users              - List semua users (dengan pagination dan filter)
GET    /users/create       - Form create user
POST   /users              - Store user baru
GET    /users/{id}/edit    - Form edit user
PUT    /users/{id}         - Update user
DELETE /users/{id}         - Delete user
GET    /users/import       - Form import Excel
POST   /users/import       - Proses import Excel
GET    /users/export/excel - Export users ke Excel
```

**Query Parameters untuk GET /users:**
- `search`: Search nama, email, atau NIM
- `role`: Filter berdasarkan role (mahasiswa/dosen)
- `page`: Halaman pagination

### Revision Management Endpoints (Dosen Only)

```
GET    /revisions          - List revisi yang dibuat dosen
GET    /revisions/create   - Form create revisi
POST   /revisions          - Store revisi baru
GET    /revisions/{id}    - Detail revisi
GET    /revisions/{id}/edit - Form edit revisi
PUT    /revisions/{id}    - Update revisi
DELETE /revisions/{id}    - Delete revisi
GET    /revisions/{id}/pdf - Export revisi ke PDF
```

### Export PDF Endpoints (Mahasiswa Only)

```
GET    /dashboard-mahasiswa/export-rekap-pdf - Export rekap revisi (statistik)
GET    /dashboard-mahasiswa/export-all-revisions-pdf - Export semua revisi lengkap
```

### Public Endpoints

```
GET    /share/{token}      - Public share link untuk revisi (tidak perlu login)
```

### Profile Endpoints

```
GET    /profile           - Edit profile form
PATCH  /profile           - Update profile
DELETE /profile           - Delete account
```

## 🗄️ Database Schema

### Users Table

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PK, auto_increment | Primary key |
| name | varchar(255) | NOT NULL | Nama lengkap |
| email | varchar(255) | NOT NULL, UNIQUE | Email user |
| email_verified_at | timestamp | NULLABLE | Timestamp verifikasi email |
| password | varchar(255) | NOT NULL | Password (hashed) |
| role | varchar(255) | NOT NULL, DEFAULT 'mahasiswa' | Role: admin, dosen, mahasiswa |
| nim | varchar(20) | NULLABLE | Nomor Induk Mahasiswa |
| judul_tugas_akhir | varchar(500) | NULLABLE | Judul tugas akhir |
| signature_path | varchar(255) | NULLABLE | Path file tanda tangan |
| dosen_pembimbing_id | bigint | NULLABLE, FK | ID dosen pembimbing |
| remember_token | varchar(100) | NULLABLE | Remember token |
| created_at | timestamp | NULLABLE | Created timestamp |
| updated_at | timestamp | NULLABLE | Updated timestamp |

**Indexes:**
- `email` (unique)
- `dosen_pembimbing_id` (index)
- `role` (index)

### Revisions Table

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | bigint | PK, auto_increment | Primary key |
| mahasiswa_id | bigint | NOT NULL, FK | ID mahasiswa |
| dosen_id | bigint | NOT NULL, FK | ID dosen |
| tahap | varchar(255) | NOT NULL | Tahap: proposal, sidang_akhir |
| tanggal_revisi | date | NOT NULL | Tanggal revisi |
| isi_revisi | text | NOT NULL | Isi revisi |
| status | varchar(255) | NOT NULL | Status: belum_diperbaiki, sudah_diperbaiki |
| token | varchar(255) | NOT NULL, UNIQUE | Token untuk share link |
| bukti_file_path | varchar(255) | NULLABLE | Path file bukti PDF |
| created_at | timestamp | NULLABLE | Created timestamp |
| updated_at | timestamp | NULLABLE | Updated timestamp |

**Indexes:**
- `token` (unique)
- `mahasiswa_id` (index)
- `dosen_id` (index)

### Notifications Table

| Column | Type | Attributes | Description |
|--------|------|------------|-------------|
| id | char(36) | PK | UUID |
| type | varchar(255) | NOT NULL | Notification type |
| notifiable_type | varchar(255) | NOT NULL | Model type |
| notifiable_id | bigint | NOT NULL | Model ID |
| data | text | NOT NULL | JSON data |
| read_at | timestamp | NULLABLE | Read timestamp |
| created_at | timestamp | NULLABLE | Created timestamp |
| updated_at | timestamp | NULLABLE | Updated timestamp |

## 🔧 Troubleshooting Detail

### Problem: Import Excel Tidak Berfungsi

**Gejala:**
- File Excel tidak bisa diupload
- Tidak ada error message yang jelas
- Data tidak masuk ke database

**Solusi:**

1. **Cek Format File:**
   ```bash
   # Pastikan file .xlsx (bukan .xls atau .csv)
   # Buka di Excel dan Save As .xlsx
   ```

2. **Cek Header:**
   - Header harus tepat: `nama_lengkap`, `email`, `password`, `role`, `nim`, `judul_tugas_akhir`, `dosen_pembimbing`
   - Case-sensitive, tidak boleh ada spasi ekstra

3. **Cek Log:**
   ```bash
   tail -f storage/logs/laravel.log
   # Atau
   php artisan tail
   ```

4. **Cek Permissions:**
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

5. **Cek Upload Size:**
   - Edit `.env`:
     ```env
     UPLOAD_MAX_SIZE=5120
     ```
   - Edit `php.ini`:
     ```ini
     upload_max_filesize = 5M
     post_max_size = 5M
     ```

### Problem: Login Loading Terus

**Gejala:**
- Halaman login loading tidak berhenti
- Tidak redirect setelah login
- Error 500 atau timeout

**Solusi:**

1. **Clear Cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   php artisan optimize:clear
   ```

2. **Cek Session:**
   ```env
   SESSION_DRIVER=file
   SESSION_LIFETIME=120
   ```

3. **Cek Database Connection:**
   ```bash
   php artisan migrate:status
   # Jika error, cek koneksi database di .env
   ```

4. **Cek Log untuk Error Detail:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

5. **Restart Server:**
   ```bash
   # Stop server (Ctrl+C)
   # Clear cache lagi
   php artisan serve
   ```

### Problem: PDF Export Error

**Gejala:**
- Error saat export PDF
- PDF kosong atau rusak
- Error 500

**Solusi:**

1. **Cek Library:**
   ```bash
   composer show barryvdh/laravel-dompdf
   # Jika tidak ada:
   composer require barryvdh/laravel-dompdf
   ```

2. **Cek Font:**
   - PDF menggunakan font Arial (default)
   - Pastikan font tersedia di sistem

3. **Cek Memory:**
   Edit `php.ini`:
   ```ini
   memory_limit = 256M
   ```

4. **Cek Storage:**
   ```bash
   php artisan storage:link
   chmod -R 775 storage
   ```

### Problem: Excel Export/Import Error

**Gejala:**
- Error saat export/import Excel
- File tidak bisa dibuka
- Data tidak lengkap

**Solusi:**

1. **Cek Library:**
   ```bash
   composer show maatwebsite/excel
   # Jika tidak ada:
   composer require maatwebsite/excel
   ```

2. **Publish Config:**
   ```bash
   php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
   ```

3. **Cek PHP Extension:**
   ```bash
   php -m | grep -i zip
   php -m | grep -i xml
   # Pastikan extension zip dan xml terinstall
   ```

## 📝 Notes

### Best Practices

1. **Backup Database Secara Berkala**
   ```bash
   # SQLite
   cp database/database.sqlite database/database.sqlite.backup
   
   # MySQL
   mysqldump -u root -p sireda > backup.sql
   ```

2. **Monitor Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Update Dependencies**
   ```bash
   composer update
   npm update
   ```

4. **Test After Changes**
   ```bash
   php artisan test
   ```

### Performance Tips

1. **Optimize Autoload:**
   ```bash
   composer dump-autoload -o
   ```

2. **Cache Configuration:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Enable OPcache** (jika menggunakan production)

## 🔒 Security

1. **Setelah Production:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Change Default Passwords**

3. **Use HTTPS**

4. **Regular Updates:**
   ```bash
   composer update
   npm update
   ```

5. **Check Dependencies:**
   ```bash
   composer audit
   npm audit
   ```

---

**Versi Dokumentasi**: 1.0.0  
**Last Updated**: November 2025

