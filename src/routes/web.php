<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\PostCommentController;

Route::get('/', fn () => redirect()->route('posts.index'));

Route::resource('posts', PostController::class);

Route::post('posts/{post}/like', PostLikeController::class)->name('posts.like');

Route::post('posts/{post}/comments', PostCommentController::class)->name('posts.comments.store');
