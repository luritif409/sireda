<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('users')->cascadeOnDelete();
            $table->string('tahap'); // 'proposal' | 'sidang_akhir'
            $table->date('tanggal_revisi');
            $table->text('isi_revisi');
            $table->string('status'); // 'belum_diperbaiki' | 'sudah_diperbaiki'
            $table->uuid('token')->unique();
            $table->string('bukti_file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisions');
    }
};












