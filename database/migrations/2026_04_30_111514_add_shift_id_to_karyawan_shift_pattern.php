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
        Schema::table('karyawan_shift_pattern', function (Blueprint $table) {
            if (Schema::hasColumn('karyawan_shift_pattern', 'shift_id')) {
                $table->dropColumn('shift_id');
            }
        });

        Schema::table('karyawan_shift_pattern', function (Blueprint $table) {
            $table->foreignId('shift_id')->nullable()->after('tipe')->constrained('shift')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan_shift_pattern', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });
    }
};
