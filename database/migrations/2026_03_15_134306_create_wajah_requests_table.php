<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wajah_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('alasan');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('catatan_admin')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('captured_at')->nullable(); // kapan karyawan capture wajah baru
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wajah_requests');
    }
};
