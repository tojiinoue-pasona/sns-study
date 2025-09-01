<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 公開投稿のみ、N+1回避のため必要な関連を先読み
        $posts = Post::query()
            ->public()
            ->with(['user:id,name', 'visibility:id,code']) // 必要な列だけ
            ->withCount('likedByUsers') 
            ->latest('id')
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        // 公開以外は404（情報漏えい防止）
        if ($post->visibility?->code !== 'public') {
            abort(404);
        }

        $post->loadMissing(['user:id,name', 'visibility:id,code', 'tags:id,name'])
             ->loadCount('likedByUsers');
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
