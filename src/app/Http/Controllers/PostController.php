<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\{Tag, Visibility, PostAttachment};
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\User; // 追加
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * 一覧
     */
    public function index()
    {
        // 公開投稿のみ取得したい場合は whereHas を利用（必要に応じて調整）
        $posts = Post::query()
            ->with(['user:id,name','visibility:id,code','attachment'])
            ->withCount('likedByUsers') // ← いいね数
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

        // ユーザーID（認証導入前の暫定）
        $userId = auth()->id() ?: \App\Models\User::query()->value('id');
        if (!$userId) {
            return back()->withInput()->with('error', 'ユーザーが存在しないため投稿できません。');
        }
        $data['user_id'] = $userId;

        $post = Post::create($data);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        // 画像保存（任意）
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('attachments', 'public');
            $post->attachment()->create([
                'path' => $path,
                'mime' => $request->file('image')->getClientMimeType(),
                'size' => $request->file('image')->getSize(),
            ]);
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
        $post->loadMissing(['tags', 'attachment']);
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

        // 新しい画像があれば置き換え
        if ($request->hasFile('image')) {
            // 旧ファイル削除
            if ($post->attachment && $post->attachment->path) {
                Storage::disk('public')->delete($post->attachment->path);
            }
            $path = $request->file('image')->store('attachments', 'public');
            $post->attachment()->updateOrCreate(
                [], // hasOne なので条件なしで1件を更新/作成
                [
                    'path' => $path,
                    'mime' => $request->file('image')->getClientMimeType(),
                    'size' => $request->file('image')->getSize(),
                ]
            );
        }

        return redirect()->route('posts.show', $post)->with('status', 'updated');
    }

    /**
     * 削除
     */
    public function destroy(Post $post)
    {
        // 添付ファイルの物理削除（FKでレコードは削除される）
        if ($post->attachment && $post->attachment->path) {
            Storage::disk('public')->delete($post->attachment->path);
        }
        $post->delete();

        return redirect()->route('posts.index')->with('status', 'deleted');
    }
}
