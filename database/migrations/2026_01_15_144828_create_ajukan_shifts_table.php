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
        Schema::create('ajukan_shifts', function (Blueprint $table) {
            $table->id();
            //relasi utama
            $table->foreignId('departemen_id')->constrained('departemen')->cascadeOnDelete();
            //relasi shift
            $table->foreignId('shift_lama_id')->constrained('shift');
            $table->foreignId('shift_baru_id')->constrained('shift');
            //periode pengajuan
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('jenis', ['sementara', 'permanen'])->default('sementara');
            //approval
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            //etc
            $table->text('alasan')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajukan_shifts');
    }
};
