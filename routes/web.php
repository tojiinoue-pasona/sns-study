<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\UserFollowController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // プロフィール管理
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // SNS機能 - Posts CUD（Create, Update, Delete）
    Route::resource('posts', PostController::class)->except(['show']);
    
    // コメント投稿/削除（認証必須）
    Route::post('/posts/{post}/comments', [PostCommentController::class, 'store'])->middleware('throttle:10,1')->name('comments.store');
    Route::delete('/comments/{comment}', [PostCommentController::class, 'destroy'])->name('comments.destroy');
    
    // いいね機能（認証必須）
    Route::post('/posts/{post}/like', [PostLikeController::class, 'store'])->middleware('throttle:20,1')->name('posts.like');
    Route::delete('/posts/{post}/like', [PostLikeController::class, 'destroy'])->middleware('throttle:20,1')->name('posts.unlike');
    
    // フォロー機能（認証必須）
    Route::post('/users/{user}/follow', [UserFollowController::class, 'store'])->middleware('throttle:20,1')->name('users.follow');
    Route::delete('/users/{user}/follow', [UserFollowController::class, 'destroy'])->middleware('throttle:20,1')->name('users.unfollow');
    
    // 検索機能（認証ユーザーのみ）
    Route::get('/search', [SearchController::class, 'index'])->name('search');
});

// 公開ページ（認証不要）
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('users.show');

require __DIR__.'/auth.php';
