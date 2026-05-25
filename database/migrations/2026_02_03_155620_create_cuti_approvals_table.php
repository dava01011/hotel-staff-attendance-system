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
        Schema::create('cuti_approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cuti_id')
                ->constrained('cuti')
                ->cascadeOnDelete();

            // Role yang berhak approve di step ini
            $table->enum('step', ['manager', 'gm', 'hrd']);

            // User yang melakukan approval
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Status approval per step
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])
                ->default('pending');

            // Catatan dari approver
            $table->text('catatan')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuti_approvals');
    }
};
