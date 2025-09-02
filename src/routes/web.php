<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;

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

Route::post('posts/{post}/like', PostLikeController::class)->name('posts.like');
