<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UserImport;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        Log::info('users.index', ['user_id' => $request->user()->id, 'step' => 'start']);

        // If the authenticated user is a dosen, show only their mahasiswa bimbingan
        if ($request->user()->role === 'dosen') {
            $query = User::where('role', 'mahasiswa')
                ->where('dosen_pembimbing_id', $request->user()->id)
                ->withCount(['revisionsAsDosen', 'revisionsAsMahasiswa'])
                ->orderBy('name');
        } else {
            $query = User::whereIn('role', ['mahasiswa', 'dosen'])
                ->withCount(['revisionsAsDosen', 'revisionsAsMahasiswa'])
                ->orderBy('role')
                ->orderBy('name');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create(Request $request): View
    {
        // Provide list of dosen for selecting pembimbing when creating mahasiswa
        $dosenList = User::where('role', 'dosen')->orderBy('name')->get();
        // Get default role from query parameter if provided
        $defaultRole = $request->query('role', '');
        return view('users.create', compact('dosenList', 'defaultRole'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:mahasiswa,dosen'],
            'dosen_pembimbing_id' => ['nullable', 'exists:users,id', 'required_if:role,mahasiswa'],
            'nim' => ['nullable', 'string', 'max:20'],
            'judul_tugas_akhir' => ['nullable', 'string', 'max:500'],
        ]);

        Log::info('users.store', [
            'user_id' => $request->user()->id,
            'step' => 'creating_user',
            'role' => $validated['role']
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'nim' => $validated['nim'] ?? null,
            'judul_tugas_akhir' => $validated['judul_tugas_akhir'] ?? null,
            'dosen_pembimbing_id' => $validated['dosen_pembimbing_id'] ?? null,
        ]);

        Log::info('users.store', [
            'user_id' => $request->user()->id,
            'step' => 'user_created',
            'created_user_id' => $user->id
        ]);

        return redirect()->route('users.index')
            ->with('success', ucfirst($validated['role']) . ' berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $dosenList = User::where('role', 'dosen')->orderBy('name')->get();
        return view('users.edit', compact('user', 'dosenList'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:mahasiswa,dosen'],
            'dosen_pembimbing_id' => ['nullable', 'exists:users,id', 'required_if:role,mahasiswa'],
            'nim' => ['nullable', 'string', 'max:20'],
            'judul_tugas_akhir' => ['nullable', 'string', 'max:500'],
        ]);

        Log::info('users.update', [
            'user_id' => $request->user()->id,
            'step' => 'updating_user',
            'target_user_id' => $user->id
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->nim = $validated['nim'] ?? null;
        $user->judul_tugas_akhir = $validated['judul_tugas_akhir'] ?? null;
        $user->dosen_pembimbing_id = $validated['dosen_pembimbing_id'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        Log::info('users.update', [
            'user_id' => $request->user()->id,
            'step' => 'user_updated',
            'target_user_id' => $user->id
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Log::info('users.destroy', [
            'user_id' => request()->user()->id,
            'step' => 'deleting_user',
            'target_user_id' => $user->id
        ]);

        $user->delete();

        Log::info('users.destroy', [
            'user_id' => request()->user()->id,
            'step' => 'user_deleted',
            'target_user_id' => $user->id
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        Log::info('users.export_excel', ['user_id' => $request->user()->id]);

        $query = User::whereIn('role', ['mahasiswa', 'dosen'])
            ->withCount(['revisionsAsDosen', 'revisionsAsMahasiswa'])
            ->orderBy('role')
            ->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        // Check if PhpSpreadsheet is available
        if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            // Fallback to CSV if PhpSpreadsheet not available
            $filename = 'users_export_' . date('Y-m-d_His') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($users) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8 Excel compatibility
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Header row
                fputcsv($file, [
                    'Nama',
                    'Email',
                    'Role',
                    'NIM',
                    'Judul Tugas Akhir'
                ], ';');
                
                // Data rows
                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->name,
                        $user->email,
                        $user->role,
                        $user->nim ?? '',
                        $user->judul_tugas_akhir ?? ''
                    ], ';');
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Use PhpSpreadsheet to create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set header row
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Role');
        $sheet->setCellValue('D1', 'NIM');
        $sheet->setCellValue('E1', 'Judul Tugas Akhir');
        
        // Style header
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');
        
        // Add data rows
        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user->name);
            $sheet->setCellValue('B' . $row, $user->email);
            $sheet->setCellValue('C' . $row, $user->role);
            $sheet->setCellValue('D' . $row, $user->nim ?? '');
            $sheet->setCellValue('E' . $row, $user->judul_tugas_akhir ?? '');
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'users_export_' . date('Y-m-d_His') . '.xlsx';
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Save to temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function showImport(): View
    {
        return view('users.import');
    }

    // Backwards-compatible wrapper: showImportForm
    public function showImportForm(): View
    {
        return $this->showImport();
    }

    // New import method using Maatwebsite\Excel and the App\Imports\UserImport class
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ]);

        Log::info('users.import.start', [
            'user_id' => $request->user()->id,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_size' => $request->file('file')->getSize(),
            'file_mime' => $request->file('file')->getMimeType()
        ]);

        $file = $request->file('file');

        try {
            // Validate file is readable
            if (!$file->isValid()) {
                throw new \Exception('File upload gagal. Silakan coba lagi.');
            }

            // Check if file exists
            if (!file_exists($file->getRealPath())) {
                throw new \Exception('File tidak ditemukan.');
            }

            Log::info('users.import.file_validated', [
                'user_id' => $request->user()->id,
                'real_path' => $file->getRealPath()
            ]);

            // Attempt import using Maatwebsite Excel
            $import = new UserImport();
            
            Log::info('users.import.before_excel_import', [
                'user_id' => $request->user()->id
            ]);

            Excel::import($import, $file);

            Log::info('users.import.after_excel_import', [
                'user_id' => $request->user()->id,
                'imported_count' => $import->imported ?? 0,
                'errors_count' => count($import->errors ?? [])
            ]);

            $imported = $import->imported ?? 0;
            $errors = $import->errors ?? [];

            // Build feedback message
            if ($imported > 0) {
                $message = "Berhasil mengimpor $imported user.";
                if (count($errors) > 0) {
                    $message .= " Terdapat " . count($errors) . " baris yang gagal atau tidak lengkap.";
                }

                Log::info('users.import.success', [
                    'user_id' => $request->user()->id,
                    'imported' => $imported,
                    'errors_count' => count($errors),
                    'errors' => $errors
                ]);

                $redirect = redirect()->route('users.index')
                    ->with('success', $message);
                
                // Always include errors if they exist
                if (count($errors) > 0) {
                    $redirect->with('import_errors', $errors);
                }
                
                return $redirect;
            } else {
                // No users imported
                $message = "Tidak ada user yang berhasil diimpor.";
                if (count($errors) > 0) {
                    $message .= " Terdapat " . count($errors) . " error.";
                } else {
                    $message .= " File mungkin kosong atau format tidak sesuai.";
                }

                Log::warning('users.import.no_imports', [
                    'user_id' => $request->user()->id,
                    'errors_count' => count($errors),
                    'errors' => $errors
                ]);

                return redirect()->route('users.import')
                    ->with('error', $message)
                    ->with('import_errors', $errors);
            }

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Maatwebsite validation errors (corrupted file, sheet not found, etc.)
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Baris " . $failure->row() . ": " . implode(", ", $failure->errors());
            }

            Log::error('users.import.validation_error', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'failures_count' => count($failures),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('users.import')
                ->with('error', 'File Excel rusak atau format tidak valid: ' . $e->getMessage())
                ->with('import_errors', $errors);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Laravel validation errors
            Log::error('users.import.laravel_validation_error', [
                'user_id' => $request->user()->id,
                'errors' => $e->errors(),
                'message' => $e->getMessage()
            ]);

            return redirect()->route('users.import')
                ->withErrors($e->errors())
                ->with('error', 'Validasi gagal: ' . $e->getMessage());

        } catch (\Exception $e) {
            // Any other exception (file reading, database, etc.)
            Log::error('users.import.error', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage();
            
            // Add more context if in debug mode
            if (config('app.debug')) {
                $errorMessage .= ' (File: ' . basename($e->getFile()) . ', Line: ' . $e->getLine() . ')';
            }

            return redirect()->route('users.import')
                ->with('error', $errorMessage);
        }
    }

    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ]);

        Log::info('users.import_excel', ['user_id' => $request->user()->id, 'step' => 'start']);

        $file = $request->file('file');
        
        $imported = 0;
        $errors = [];

        try {
            // Check if PhpSpreadsheet is available
            if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                return redirect()->route('users.import')
                    ->with('error', 'Library PhpSpreadsheet tidak ditemukan. Silakan install dengan: composer require phpoffice/phpspreadsheet');
            }

            // Load Excel file
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows) || count($rows) < 2) {
                throw new \Exception('File Excel kosong atau tidak memiliki data');
            }

            // Get header row (first row)
            $header = array_map('trim', array_map('strval', $rows[0]));
            
            // Find column indices (case insensitive)
            $nameIndex = false;
            $emailIndex = false;
            $roleIndex = false;
            $nimIndex = false;
            $judulIndex = false;
            
            foreach ($header as $idx => $col) {
                $colLower = strtolower(trim($col));
                if ($nameIndex === false && $colLower === 'nama') {
                    $nameIndex = $idx;
                }
                if ($emailIndex === false && $colLower === 'email') {
                    $emailIndex = $idx;
                }
                if ($roleIndex === false && $colLower === 'role') {
                    $roleIndex = $idx;
                }
                if ($nimIndex === false && $colLower === 'nim') {
                    $nimIndex = $idx;
                }
                if ($judulIndex === false && ($colLower === 'judul tugas akhir' || $colLower === 'judul_tugas_akhir')) {
                    $judulIndex = $idx;
                }
            }
            
            // Validate required columns
            if ($nameIndex === false || $emailIndex === false || $roleIndex === false) {
                throw new \Exception('Header tidak valid. Kolom wajib: Nama, Email, Role. Kolom opsional: NIM, Judul Tugas Akhir');
            }
            
            // If optional columns not found, set to null
            if ($nimIndex === false) {
                $nimIndex = null;
            }
            if ($judulIndex === false) {
                $judulIndex = null;
            }

            // Process data rows (skip header row)
            for ($lineNumber = 2; $lineNumber <= count($rows); $lineNumber++) {
                $row = $rows[$lineNumber - 1];
                
                // Skip empty rows
                if (empty(array_filter($row, function($v) { return trim((string)$v) !== ''; }))) {
                    continue;
                }
                
                // Get values from columns
                $name = trim((string)($row[$nameIndex] ?? ''));
                $email = trim((string)($row[$emailIndex] ?? ''));
                $role = strtolower(trim((string)($row[$roleIndex] ?? '')));
                $nim = ($nimIndex !== null) ? trim((string)($row[$nimIndex] ?? '')) : '';
                $judulTugasAkhir = ($judulIndex !== null) ? trim((string)($row[$judulIndex] ?? '')) : '';
                
                if (empty($name) || empty($email) || empty($role)) {
                    $errors[] = "Baris $lineNumber: Nama, Email, dan Role wajib diisi (Nama: '$name', Email: '$email', Role: '$role')";
                    continue;
                }
                
                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Baris $lineNumber: Format email tidak valid: '$email'";
                    continue;
                }
                
                if (!in_array($role, ['mahasiswa', 'dosen'])) {
                    $errors[] = "Baris $lineNumber: Role harus 'mahasiswa' atau 'dosen', ditemukan: '$role'";
                    continue;
                }
                
                // Check if email already exists
                if (User::where('email', $email)->exists()) {
                    $errors[] = "Baris $lineNumber: Email '$email' sudah terdaftar";
                    continue;
                }
                
                // Generate default password
                $defaultPassword = 'password123';
                
                try {
                    User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($defaultPassword),
                        'role' => $role,
                        'nim' => !empty($nim) && $nim !== '-' ? $nim : null,
                        'judul_tugas_akhir' => !empty($judulTugasAkhir) && $judulTugasAkhir !== '-' ? $judulTugasAkhir : null,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris $lineNumber: " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            Log::error('users.import_excel.error', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('users.import')
                ->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }

        Log::info('users.import_excel', [
            'user_id' => $request->user()->id,
            'imported' => $imported,
            'errors_count' => count($errors)
        ]);

        $message = "Berhasil mengimpor $imported user.";
        if (count($errors) > 0) {
            $message .= " Terdapat " . count($errors) . " error.";
            session()->flash('import_errors', $errors);
        }

        if ($imported === 0 && count($errors) > 0) {
            return redirect()->route('users.import')
                ->with('error', $message)
                ->with('import_errors', $errors);
        }

        return redirect()->route('users.index')
            ->with('success', $message);
    }
}

