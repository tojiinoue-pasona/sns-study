<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\{Tag, Visibility};
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    /**
     * 一覧
     */
    public function index()
    {
        // 一覧は閲覧可能な投稿のみに限定
        $query = Post::query()
            ->withBasics()
            ->withLikeCount()
            ->latestFirst();

        if (!auth()->check()) {
            // ゲストは public のみ
            $query->whereHas('visibility', fn($q) => $q->where('code', 'public'));
        } else {
            $user = auth()->user();
            if (!$user->isAdmin()) {
                // 一般ユーザー: public または 自分の投稿 または フォロー中ユーザーの followers 限定投稿
                $query->where(function ($q) use ($user) {
                    $q->whereHas('visibility', fn($v) => $v->where('code', 'public'))
                        ->orWhere('user_id', $user->id)
                        ->orWhere(function ($q2) use ($user) {
                            $q2->whereHas('visibility', fn($v) => $v->where('code', 'followers'))
                               ->whereExists(function ($sub) use ($user) {
                                   $sub->select(DB::raw(1))
                                       ->from('follows')
                                       ->whereColumn('follows.followee_id', 'posts.user_id')
                                       ->where('follows.follower_id', $user->id);
                               });
                        });
                });
            }
            // 管理者は制限なし
        }

        $posts = $query->paginate(10);

        return view('posts.index', compact('posts'));
    }

    /**
     * 作成画面
     */
    public function create()
    {
        $this->authorize('create', Post::class);
        
        $visibilities = Visibility::select('id','code')->get();
        $tags = Tag::orderBy('name')->get(['id','name']);
        return view('posts.create', compact('visibilities','tags'));
    }

    /**
     * 保存
     */
    public function store(PostStoreRequest $request)
    {
        $this->authorize('create', Post::class);
        
        $data = $request->validated();

        $userId = auth()->id();
        if (!$userId) {
            return back()->withInput()->with('error', 'ログインが必要です。');
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
        $this->authorize('view', $post);
        
        $post->load([
            'user',
            'tags',
            'visibility',
            'comments.user',
            // load users who liked this post (only needed fields)
            'likedByUsers:id,name,avatar',
        ])->loadCount('likedByUsers');

        // ビューでのクエリ実行を避けるため、フォロー状態をここで算出
        $isFollowing = false;
        if (auth()->check() && auth()->id() !== $post->user_id) {
            $isFollowing = DB::table('follows')
                ->where('follower_id', auth()->id())
                ->where('followee_id', $post->user_id)
                ->exists();
        }

        return view('posts.show', ['post' => $post, 'isFollowing' => $isFollowing]);
    }

    /**
     * 編集画面
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        
        $visibilities = Visibility::select('id','code')->get();
        $tags = Tag::orderBy('name')->get(['id','name']);
        return view('posts.edit', compact('post','visibilities','tags'));
    }

    /**
     * 更新
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        $this->authorize('update', $post);
        
        $this->postService->update($post, $request->validated(), $request->file('image'), $request);

        return redirect()->route('posts.show', $post)->with('status', 'updated');
    }

    /**
     * 削除
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        
        // 添付削除は Policy 実装時に検討
        $post->delete();
        return redirect()->route('posts.index')->with('status', 'deleted');
    }
}
