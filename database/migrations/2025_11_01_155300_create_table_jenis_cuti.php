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
        Schema::create('jenis_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            // contoh: Cuti Tahunan, Cuti Melahirkan, Cuti Sakit

            $table->text('deskripsi')->nullable();

            $table->boolean('butuh_file')->default(false);
            // misal: melahirkan, sakit → true

            $table->boolean('potong_jatah')->default(true);
            // cuti khusus bisa false

            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_cuti');
    }
};
