<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Post;

$posts = Post::with('user')->orderBy('id')->get();
foreach ($posts as $post) {
    $u = $post->user;
    echo sprintf("post:%s | user:%s(%s) | avatar:%s\n", $post->id, $u->name ?? 'NULL', $u->id ?? 'NULL', $u->avatar ?? 'NULL');
}
