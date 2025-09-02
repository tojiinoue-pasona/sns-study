<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', fn () => redirect()->route('posts.index'));

Route::resource('posts', PostController::class)
    ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'])
    ->names([
        'index' => 'posts.index',
        'show'  => 'posts.show',
        'destroy' => 'posts.destroy',
        'create' => 'posts.create',
        'store' => 'posts.store',
        'edit' => 'posts.edit',
        'update' => 'posts.update',
    ]);
