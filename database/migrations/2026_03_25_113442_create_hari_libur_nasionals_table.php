<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Simplified: 1 tabel saja untuk hari libur nasional
     * Menggabungkan master data (fixed/dynamic) dengan instance per tahun
     */
    public function up(): void
    {
        Schema::create('hari_libur_nasional', function (Blueprint $table) {
            $table->id();

            // Tanggal hari libur nasional (tanggal merah)
            $table->date('tanggal')->unique();

            // Nama hari libur (Lebaran, Natal, Tahun Baru, dll)
            $table->string('nama');

            // Tipe:
            // - fixed: tanggal tetap setiap tahun (1 Januari, 25 Desember)
            // - dynamic: tanggal berbeda per tahun (Idul Fitri, Idul Adha)
            // - manual: input manual khusus (cuti bersama ad-hoc)
            $table->enum('tipe', ['fixed', 'dynamic', 'manual'])->default('dynamic');

            // ===== UNTUK FIXED =====
            // Bulan & hari tetap (untuk tipe = 'fixed')
            // Jika isi, sistem akan auto-recur setiap tahun
            $table->unsignedTinyInteger('bulan_tetap')->nullable(); // 1-12
            $table->unsignedTinyInteger('hari_tetap')->nullable();   // 1-31

            // ===== TRACKING =====
            // Tahun berapa record ini dibuat
            $table->unsignedSmallInteger('tahun')->nullable();

            // Untuk fixed: apakah auto-recur ke tahun depan?
            $table->boolean('is_recurring')->default(false); // true = auto-repeat setiap tahun

            // Keterangan
            $table->text('keterangan')->nullable();

            // Status aktif/tidak
            $table->boolean('is_active')->default(true);

            // Timestamps
            $table->timestamps();

            // Index
            $table->index(['tanggal', 'is_active']);
            $table->index(['tipe', 'tahun']);
            $table->index(['is_recurring', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hari_libur_nasional');
    }
};
