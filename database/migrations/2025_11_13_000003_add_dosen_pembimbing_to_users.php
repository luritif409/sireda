<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'dosen_pembimbing_id')) {
                $table->unsignedBigInteger('dosen_pembimbing_id')->nullable()->after('signature_path')->index();
                // add foreign key if supported
                try {
                    $table->foreign('dosen_pembimbing_id')->references('id')->on('users')->onDelete('set null');
                } catch (\Exception $e) {
                    // Some SQLite setups disable foreign keys at table-alter; ignore if it fails
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'dosen_pembimbing_id')) {
                // drop foreign if exists
                try {
                    $table->dropForeign(['dosen_pembimbing_id']);
                } catch (\Exception $e) {
                    // ignore
                }

                $table->dropColumn('dosen_pembimbing_id');
            }
        });
    }
};
