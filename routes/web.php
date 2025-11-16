<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RevisiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard umum - redirect berdasarkan role
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if (!$user->role) {
            return redirect('/')->with('error', 'Role tidak ditemukan. Silakan hubungi administrator.');
        }
        if ($user->role === 'dosen') {
            return redirect()->route('dashboard.dosen');
        } elseif ($user->role === 'mahasiswa') {
            return redirect()->route('dashboard.mahasiswa');
        } elseif ($user->role === 'admin') {
            return redirect()->route('dashboard.admin');
        }
        return redirect('/');
    })->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard-admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
    // Define specific routes before resource route to avoid conflicts
    Route::get('users/export/excel', [\App\Http\Controllers\UserController::class, 'exportExcel'])->name('users.export.excel');
    Route::get('users/import', [\App\Http\Controllers\UserController::class, 'showImportForm']);
    Route::post('users/import', [\App\Http\Controllers\UserController::class, 'import'])->name('users.import');
    Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
});

Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::resource('revisions', RevisiController::class);
    Route::get('revisions/{revision}/pdf', [RevisiController::class, 'exportPDF'])->name('revisions.pdf');
    Route::patch('revisions/{revision}/update-status', [RevisiController::class, 'updateStatus'])->name('revisions.update-status');
    Route::get('mahasiswa-bimbingan', [RevisiController::class, 'mahasiswaBimbingan'])->name('mahasiswa.bimbingan');
    Route::get('/dashboard-dosen', [DashboardController::class, 'dosen'])->name('dashboard.dosen');
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard-mahasiswa', [DashboardController::class, 'mahasiswa'])->name('dashboard.mahasiswa');
    Route::get('/dashboard-mahasiswa/export-rekap-pdf', [DashboardController::class, 'exportRekapPDF'])->name('dashboard.mahasiswa.export-rekap-pdf');
    Route::get('/dashboard-mahasiswa/export-all-revisions-pdf', [DashboardController::class, 'exportAllRevisionsPDF'])->name('dashboard.mahasiswa.export-all-revisions-pdf');
});

Route::get('share/{token}', [RevisiController::class, 'share'])->name('revisions.share');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
