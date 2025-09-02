<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\{Tag, Visibility};
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\User; // 追加

class PostController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        // 公開投稿のみ取得したい場合は whereHas を利用（必要に応じて調整）
        $posts = Post::query()
            ->with(['user:id,name', 'visibility:id,code'])
            ->latest('id')
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

        // 認証導入後: 必ず auth()->id() を使う
        $userId = auth()->id();

        // 未導入の暫定対応：既存ユーザーのIDを利用（なければエラーに）
        if (!$userId) {
            $userId = User::query()->value('id'); // 最初のユーザーID
            if (!$userId) {
                return back()->withInput()->with('error', 'ユーザーが存在しないため投稿できません。先にユーザーを作成してください。');
            }
        }

        $data['user_id'] = $userId;

        $post = Post::create($data);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        return redirect()->route('posts.show', $post)->with('status', 'created');
    }

    /**
     * 詳細
     */
    public function show(Post $post)
    {
        // 可視性の簡易チェック（認証導入後は Policy::view に集約）
        if ($post->visibility?->code !== 'public') {
            // public 以外は 404（要件により調整）
            // abort(404);
        }

        $post->loadMissing(['user:id,name', 'visibility:id,code', 'tags:id,name']);
        return view('posts.show', compact('post'));
    }

    /**
     * 編集画面
     */
    public function edit(Post $post)
    {
        $post->loadMissing('tags');
        $visibilities = Visibility::select('id','code')->get();
        $tags = Tag::orderBy('name')->get(['id','name']);
        return view('posts.edit', compact('post','visibilities','tags'));
    }

    /**
     * 更新
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        $data = $request->validated();

        $post->update($data);

        if (array_key_exists('tags', $data)) {
            $post->tags()->sync($data['tags'] ?? []);
        }

        return redirect()->route('posts.show', $post)->with('status', 'updated');
    }

    /**
     * 削除
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')->with('status', 'deleted');
    }
}
