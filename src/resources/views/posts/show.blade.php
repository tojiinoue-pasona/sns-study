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

<section class="card" style="margin-top:16px;">
  <h3>コメント</h3>

  @forelse($post->comments as $comment)
    <div style="border-top:1px solid #eee; padding-top:8px; margin-top:8px;">
      <div class="muted">by {{ $comment->user->name }} ・ {{ $comment->created_at->diffForHumans() }}</div>
      <div style="white-space:pre-wrap;">{{ $comment->body }}</div>
    </div>
  @empty
    <div class="muted">最初のコメントを書きましょう。</div>
  @endforelse
</section>

<section class="card" style="margin-top:12px;">
  <h3>コメントを書く</h3>
  <form method="POST" action="{{ route('posts.comments.store', $post) }}">
    @csrf
    <div class="field">
      <textarea name="body" rows="4" placeholder="コメントを入力...">{{ old('body') }}</textarea>
      @error('body') <div class="error-text">{{ $message }}</div> @enderror
    </div>
    <button type="submit">送信</button>
  </form>
</section>
@endsection
