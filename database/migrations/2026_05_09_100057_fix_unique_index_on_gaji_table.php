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
        Schema::table('gaji', function (Blueprint $table) {
            // Drop incorrect unique indexes
            $table->dropUnique('gaji_bulan_unique');
            $table->dropUnique('gaji_tahun_unique');

            // Add correct composite unique index
            $table->unique(['karyawan_id', 'bulan', 'tahun'], 'gaji_karyawan_bulan_tahun_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji', function (Blueprint $table) {
            $table->dropUnique('gaji_karyawan_bulan_tahun_unique');
            $table->unique('bulan', 'gaji_bulan_unique');
            $table->unique('tahun', 'gaji_tahun_unique');
        });
    }
};
