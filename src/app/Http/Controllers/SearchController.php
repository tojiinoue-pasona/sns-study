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
            ->withBasics()
            ->withLikeCount();

        if ($q !== null && $q !== '') {
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
            $pattern = "%{$escaped}%";
            $query->whereRaw("posts.body LIKE ? ESCAPE '\\\\'", [$pattern]);
        }

        if (!empty($tag)) {
            $query->join('post_tags', 'post_tags.post_id', '=', 'posts.id')
                  ->where('post_tags.tag_id', $tag)
                  ->distinct('posts.id');
        }

        $posts = $query->latest('posts.id')->paginate(10)->appends($data);
        $tags = Tag::query()->orderBy('name')->get(['id','name']);

        return view('search.index', compact('posts','tags') + ['q'=>$q,'tag'=>$tag]);
    }
}