<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$m = App\Models\MateriFile::find(1);
if (! $m) {
    echo "MateriFile not found\n";
    exit;
}
$fp = $m->file_path;
$clean = str_replace('public/', '', $fp);
$p1 = storage_path('app/' . $fp);
$p2 = storage_path('app/public/' . $clean);
$p3 = storage_path('app/' . $clean);
echo "file_path: " . $fp . "\n";
echo "clean: " . $clean . "\n";
echo "path1: " . $p1 . "\n";
echo "path2: " . $p2 . "\n";
echo "path3: " . $p3 . "\n";
$paths = array($p1, $p2, $p3);
foreach ($paths as $p) {
    echo $p . ': ' . (file_exists($p) ? 'exists' : 'missing') . "\n";
}
