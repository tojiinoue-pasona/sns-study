<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$posts = App\Models\Post::with('user')->limit(20)->get();
foreach ($posts as $p) {
    $u = $p->user;
    echo "post:" . $p->id . " user:" . ($u->id ?? 'NULL') . " name:" . ($u->name ?? 'NULL') . " avatar:" . ($u->avatar ?? 'NULL') . PHP_EOL;
}
