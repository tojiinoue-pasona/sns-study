<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Post;
use App\Models\Tag;
use App\Helpers\SqlSecurityHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(SearchRequest $request)
    {
        $data = $request->validated();
        $q    = $data['q']   ?? null;
        $tag  = $data['tag'] ?? null;

        $query = Post::query()
            ->select('posts.*')
            ->withBasics()
            ->withLikeCount();

        // 認可: 検索結果は閲覧可能な投稿のみに限定
        $user = Auth::user();
        if ($user && !$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('visibility', fn($v) => $v->where('code', 'public'))
                  ->orWhere('posts.user_id', $user->id)
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
        } elseif (!$user) {
            // 念のためゲスト時（通常はauthで保護されている）
            $query->whereHas('visibility', fn($v) => $v->where('code', 'public'));
        }

        if ($q !== null && $q !== '') {
            // SQL Injection対策: 入力値の安全性チェック
            $validation = SqlSecurityHelper::validateSearchInput($q);
            
            if (!$validation['safe']) {
                // 危険なパターンが検出された場合はログに記録
                Log::warning('Potential SQL injection attempt detected', [
                    'input' => $q,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
                
                // 安全でない場合は検索を実行しない
                $posts = collect();
            } else {
                // 安全なLIKE検索を実行
                SqlSecurityHelper::safeLike($query, 'posts.body', $q);
            }
        }

        if (!empty($tag)) {
            $query->join('post_tags', 'post_tags.post_id', '=', 'posts.id')
                  ->where('post_tags.tag_id', $tag)
                  ->distinct('posts.id');
        }

        $posts = isset($posts) && $posts->isEmpty()
            ? collect()
            : $query->latest('posts.id')->paginate(10)->appends($data);
            
        $tags = Tag::query()->orderBy('name')->get(['id','name']);

        return view('search.index', compact('posts','tags') + ['q'=>$q,'tag'=>$tag]);
    }
}
