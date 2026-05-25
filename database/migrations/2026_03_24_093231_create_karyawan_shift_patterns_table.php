<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('karyawan_shift_pattern', function (Blueprint $table) {
            $table->id();

            // Foreign key ke karyawan
            $table->unsignedBigInteger('karyawan_id');
            $table->foreign('karyawan_id')
                ->references('id')
                ->on('karyawan')
                ->onDelete('cascade');

            // Hari dalam seminggu: 'minggu', 'senin', 'selasa', etc
            $table->enum('hari', ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);

            // Tipe: kerja atau libur
            $table->enum('tipe', ['kerja', 'libur']);

            // ===== LEVEL 1: DEFAULT/PERMANENT PATTERN =====
            // Tandai apakah ini pattern default (berlaku selamanya sampai diubah)
            $table->boolean('is_default')->default(false);

            // ===== LEVEL 2: WEEKLY OVERRIDE PATTERN =====
            // Minggu ke berapa (1-52) untuk override mingguan
            $table->unsignedTinyInteger('minggu_ke')->nullable(); // NULL = bukan weekly override

            // Tahun untuk weekly override
            $table->unsignedSmallInteger('tahun')->nullable(); // NULL = bukan weekly override

            // Status aktif/tidak
            $table->boolean('is_active')->default(true);

            // Timestamps
            $table->timestamps();

            // Indexes untuk query
            // Query default pattern
            $table->index(['karyawan_id', 'is_default', 'is_active'], 'idx_ksp_default_pattern');

            // Query weekly override pattern
            $table->index(['karyawan_id', 'minggu_ke', 'tahun', 'is_active'], 'idx_ksp_weekly_pattern');

            // Query umum
            $table->index(['karyawan_id', 'hari', 'is_active'], 'idx_ksp_karyawan_hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan_shift_pattern');
    }
};
