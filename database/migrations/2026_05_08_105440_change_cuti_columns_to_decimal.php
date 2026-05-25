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
        Schema::table('jabatan', function (Blueprint $table) {
            $table->decimal('jatah_cuti_bulanan', 8, 1)->change();
        });

        Schema::table('jatah_cuti', function (Blueprint $table) {
            $table->decimal('jatah_awal', 8, 1)->change();
            $table->decimal('jatah', 8, 1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->integer('jatah_cuti_bulanan')->change();
        });

        Schema::table('jatah_cuti', function (Blueprint $table) {
            $table->integer('jatah_awal')->change();
            $table->integer('jatah')->change();
        });
    }
};
