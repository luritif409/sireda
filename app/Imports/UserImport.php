<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;

class UserImport implements ToModel, WithHeadingRow, SkipsEmptyRows, SkipsOnError
{
    use Importable;

    public int $imported = 0;
    public array $errors = [];
    public int $rowNumber = 0;

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowNumber++;
        $lineNumber = $this->rowNumber + 1; // +1 because heading row is row 1

        // Log to confirm import is triggered
        Log::info('UserImport.model() called', [
            'row_number' => $lineNumber,
            'row_data' => $row
        ]);

        try {
            // Normalize keys to lowercase for case-insensitive access
            $normalizedRow = [];
            foreach ($row as $key => $value) {
                $normalizedRow[strtolower(trim((string)$key))] = $value;
            }

            // Extract and trim values
            $name = trim((string)($normalizedRow['nama_lengkap'] ?? ''));
            $email = trim((string)($normalizedRow['email'] ?? ''));
            $password = trim((string)($normalizedRow['password'] ?? ''));
            $role = strtolower(trim((string)($normalizedRow['role'] ?? '')));
            $nim = trim((string)($normalizedRow['nim'] ?? '')) ?: null;
            $judul = trim((string)($normalizedRow['judul_tugas_akhir'] ?? '')) ?: null;
            $dosen = trim((string)($normalizedRow['dosen_pembimbing'] ?? ''));

            // Validate required fields
            if (empty($name) || empty($email) || empty($role)) {
                $this->errors[] = "Baris $lineNumber: Kolom 'nama_lengkap', 'email', dan 'role' wajib diisi.";
                Log::warning('UserImport: Missing required fields', [
                    'line' => $lineNumber,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ]);
                return null; // Skip this row
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "Baris $lineNumber: Format email tidak valid: '$email'.";
                Log::warning('UserImport: Invalid email format', [
                    'line' => $lineNumber,
                    'email' => $email
                ]);
                return null; // Skip this row
            }

            // Validate role
            if ($role !== 'mahasiswa' && $role !== 'dosen') {
                $this->errors[] = "Baris $lineNumber: Role harus 'mahasiswa' atau 'dosen', ditemukan: '$role'.";
                Log::warning('UserImport: Invalid role', [
                    'line' => $lineNumber,
                    'role' => $role
                ]);
                return null; // Skip this row
            }

            // Check for duplicate email
            if (User::where('email', $email)->exists()) {
                $this->errors[] = "Baris $lineNumber: Email '$email' sudah terdaftar.";
                Log::warning('UserImport: Duplicate email', [
                    'line' => $lineNumber,
                    'email' => $email
                ]);
                return null; // Skip this row
            }

            // Match dosen_pembimbing by email or name (if provided)
            // Only for mahasiswa role
            $dosenId = null;
            if ($role === 'mahasiswa' && !empty($dosen)) {
                $dosenModel = User::where('role', 'dosen')
                    ->where(function ($q) use ($dosen) {
                        $q->where('email', $dosen)
                          ->orWhere('name', 'like', "%{$dosen}%");
                    })->first();

                if ($dosenModel) {
                    $dosenId = $dosenModel->id;
                } else {
                    $this->errors[] = "Baris $lineNumber: Dosen pembimbing '$dosen' tidak ditemukan.";
                    Log::warning('UserImport: Dosen pembimbing not found', [
                        'line' => $lineNumber,
                        'dosen' => $dosen
                    ]);
                    return null; // Skip this row
                }
            } elseif ($role === 'dosen' && !empty($dosen)) {
                // Dosen tidak perlu pembimbing, warn but don't fail
                Log::info('UserImport: Dosen pembimbing provided for dosen role, ignoring', [
                    'line' => $lineNumber,
                    'dosen' => $dosen
                ]);
            }

            // Generate default password if empty
            if (empty($password)) {
                $password = Str::random(12);
                Log::info('UserImport: Generated password for user', [
                    'line' => $lineNumber,
                    'email' => $email
                ]);
            }

            // Create and return User model instance
            $user = new User([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => $role,
                'nim' => $nim,
                'judul_tugas_akhir' => $judul,
                'dosen_pembimbing_id' => $dosenId,
            ]);

            $this->imported++;
            Log::info('UserImport: User model created successfully', [
                'line' => $lineNumber,
                'email' => $email,
                'role' => $role,
                'imported_count' => $this->imported
            ]);

            return $user;

        } catch (\Throwable $e) {
            // Catch any unexpected error during row processing
            $this->errors[] = "Baris $lineNumber: Kesalahan tidak terduga - " . $e->getMessage();
            Log::error('UserImport: Unexpected error in model()', [
                'line' => $lineNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'row_data' => $row
            ]);
            return null; // Skip this row
        }
    }

    /**
     * Handle errors when an exception occurs during import
     * This catches any unexpected errors that might occur outside of model()
     */
    public function onError(\Throwable $e)
    {
        $lineNumber = $this->rowNumber + 1;
        $this->errors[] = "Baris $lineNumber: Kesalahan tidak terduga - " . $e->getMessage();
        Log::error('UserImport: Error caught by SkipsOnError', [
            'row' => $lineNumber,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
