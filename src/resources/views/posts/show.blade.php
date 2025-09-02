@extends('layouts.app')

@section('content')
<p><a href="{{ route('posts.index') }}">← Back to Posts</a></p>

<article class="card">
  <div class="muted">by {{ $post->user->name }} ・ {{ $post->visibility->code }}</div>

  @if($post->attachment)
    <div style="margin-top:12px">
      <img src="{{ asset('storage/'.$post->attachment->path) }}" alt="" style="max-width:100%;height:auto;border:1px solid #ddd;border-radius:8px;">
    </div>
  @endif

  <p style="white-space:pre-wrap; margin-top:12px">{{ $post->body }}</p>

  @if($post->tags->isNotEmpty())
    <div style="margin-top:12px">
      @foreach($post->tags as $tag)
        <span class="tag">{{ $tag->name }}</span>
      @endforeach
    </div>
  @endif

  <div class="muted" style="margin-top:12px; display:flex; gap:8px; align-items:center;">
    <button class="like-btn" data-post-id="{{ $post->id }}" data-url="{{ route('posts.like', $post) }}">いいね</button>
    Likes: <span class="like-count" data-post-id="{{ $post->id }}">{{ $post->liked_by_users_count ?? 0 }}</span>
  </div>
</article>
@endsection
