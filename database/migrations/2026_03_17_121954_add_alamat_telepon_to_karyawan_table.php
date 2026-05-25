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
Schema::table('karyawan', function (Blueprint $table) {

    if (!Schema::hasColumn('karyawan', 'no_telepon')) {
        $table->string('no_telepon', 20)->nullable()->after('foto_profil');
    }

    if (!Schema::hasColumn('karyawan', 'alamat')) {
        $table->text('alamat')->nullable()->after('no_telepon');
    }

});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            //
        });
    }
};
