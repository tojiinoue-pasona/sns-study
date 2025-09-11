<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\UserFollowController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// セッション認証が必要なAPIエンドポイント
Route::middleware(['auth', 'web'])->group(function () {
    // Posts CRUD API
    Route::apiResource('posts', PostController::class);
    
    // コメント API
    Route::post('/posts/{post}/comments', [PostCommentController::class, 'store']);
    Route::delete('/comments/{comment}', [PostCommentController::class, 'destroy']);
    
    // いいね API
    Route::post('/posts/{post}/like', [PostLikeController::class, 'store']);
    Route::delete('/posts/{post}/like', [PostLikeController::class, 'destroy']);
    
    // フォロー API
    Route::post('/users/{user}/follow', [UserFollowController::class, 'store']);
    Route::delete('/users/{user}/follow', [UserFollowController::class, 'destroy']);
});

// 公開API（認証不要）
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);
