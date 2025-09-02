<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(SearchRequest $request)
    {
        $data = $request->validated();
        $q    = $data['q']   ?? null;
        $tag  = $data['tag'] ?? null;

        $query = Post::query()
            ->select('posts.*')
            ->with(['user:id,name', 'visibility:id,code', 'attachment'])
            ->withCount('likedByUsers');

        // 本文 LIKE（ワイルドカードをエスケープ）
        if ($q !== null && $q !== '') {
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
            $pattern = "%{$escaped}%";
            $query->whereRaw("posts.body LIKE ? ESCAPE '\\\\'", [$pattern]);
        }

        // タグ絞り込み（JOIN）
        if (!empty($tag)) {
            $query->join('post_tags', 'post_tags.post_id', '=', 'posts.id')
                  ->where('post_tags.tag_id', $tag)
                  ->distinct('posts.id');
        }

        // 空検索は全件表示（方針）
        $posts = $query->latest('posts.id')->paginate(10)->appends($data);

        $tags = Tag::query()->orderBy('name')->get(['id','name']);

        return view('search.index', [
            'posts' => $posts,
            'tags'  => $tags,
            'q'     => $q,
            'tag'   => $tag,
        ]);
    }
}