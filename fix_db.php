<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('karyawan_shift_pattern', function (Blueprint $table) {
    if (Schema::hasColumn('karyawan_shift_pattern', 'shift_id')) {
        $table->dropColumn('shift_id');
        echo "Dropped shift_id column.\n";
    }
});
