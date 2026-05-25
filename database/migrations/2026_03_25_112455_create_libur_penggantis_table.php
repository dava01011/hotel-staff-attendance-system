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
        Schema::create('libur_pengganti', function (Blueprint $table) {
            $table->id();

            // Foreign key ke karyawan
            $table->unsignedBigInteger('karyawan_id');
            $table->foreign('karyawan_id')
                ->references('id')
                ->on('karyawan')
                ->onDelete('cascade');

            // Saldo libur pengganti (jumlah hari)
            $table->unsignedInteger('saldo')->default(0);

            // Tanggal terakhir di-update
            $table->timestamp('terakhir_diupdate')->nullable();

            // Timestamps
            $table->timestamps();

            // Unique constraint: 1 record per karyawan
            $table->unique('karyawan_id');

            // Index
            $table->index(['karyawan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('libur_pengganti');
    }
};
