<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['users', 'employees', 'employee_documents', 'departments'];
foreach ($tables as $table) {
    try {
        $count = Illuminate\Support\Facades\DB::table($table)->count();
        echo $table.'='.$count.PHP_EOL;
    } catch (Throwable $e) {
        echo $table.'=MISSING ('.$e->getMessage().')'.PHP_EOL;
    }
}

$cols = Illuminate\Support\Facades\Schema::getColumnListing('employees');
echo 'employee_columns='.implode(',', $cols).PHP_EOL;
