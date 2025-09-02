<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\{Tag, Visibility};
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\User;
use App\Services\PostService;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    /**
     * 一覧
     */
    public function index()
    {
        // 共通読み込みはスコープ化
        $posts = Post::query()
            ->withBasics()
            ->withLikeCount()
            ->latestFirst()
            ->paginate(10);

        return view('posts.index', compact('posts'));
    }

    /**
     * 作成画面
     */
    public function create()
    {
        $visibilities = Visibility::select('id','code')->get();
        $tags = Tag::orderBy('name')->get(['id','name']);
        return view('posts.create', compact('visibilities','tags'));
    }

    /**
     * 保存
     */
    public function store(PostStoreRequest $request)
    {
        $data = $request->validated();

        $userId = auth()->id() ?: User::query()->value('id');
        if (!$userId) {
            return back()->withInput()->with('error', 'ユーザーが存在しないため投稿できません。');
        }
        $data['user_id'] = $userId;

        $post = $this->postService->create($data, $request->file('image'), $request);

        return redirect()->route('posts.show', $post)->with('status', 'created');
    }

    /**
     * 詳細
     */
    public function show(Post $post)
    {
        $post->loadMissing([
            'user:id,name',
            'visibility:id,code',
            'tags:id,name',
            'attachment',
            'comments.user:id,name',
        ])->loadCount('likedByUsers');

        return view('posts.show', compact('post'));
    }

    /**
     * 編集画面
     */
    public function edit(Post $post)
    {
        $visibilities = Visibility::select('id','code')->get();
        $tags = Tag::orderBy('name')->get(['id','name']);
        return view('posts.edit', compact('post','visibilities','tags'));
    }

    /**
     * 更新
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        $this->postService->update($post, $request->validated(), $request->file('image'), $request);

        return redirect()->route('posts.show', $post)->with('status', 'updated');
    }

    /**
     * 削除
     */
    public function destroy(Post $post)
    {
        // 添付削除は Policy 実装時に検討
        $post->delete();
        return redirect()->route('posts.index')->with('status', 'deleted');
    }
}
