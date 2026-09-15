<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MateriFile;

$paths = MateriFile::pluck('file_path')->unique();
foreach ($paths as $path) {
    echo $path . "\n";
}
