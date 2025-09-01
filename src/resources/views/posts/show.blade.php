@extends('layouts.app')

@section('content')
<p><a href="{{ route('posts.index') }}">← Back to Posts</a></p>

<article class="card">
  <div class="muted">by {{ $post->user->name }} ・ {{ $post->visibility->code }}</div>
  <p style="white-space:pre-wrap; margin-top:8px">{{ $post->body }}</p>

  @if($post->tags->isNotEmpty())
    <div style="margin-top:12px">
      @foreach($post->tags as $tag)
        <span class="tag">{{ $tag->name }}</span>
      @endforeach
    </div>
  @endif

  <div class="muted" style="margin-top:12px">
    Likes: {{ $post->liked_by_users_count }}
  </div>
</article>
@endsection
