@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('content')
<h1>Posts</h1>

@foreach ($posts as $post)
  <article class="card" style="display:flex;gap:12px;align-items:flex-start;">
    @if($post->attachment)
      <img src="{{ asset('storage/'.$post->attachment->path) }}" alt="" style="width:96px;height:96px;object-fit:cover;border:1px solid #ddd;border-radius:8px;">
    @endif
    <div style="flex:1">
      <p>{{ Str::limit($post->body, 140) }}</p>
      <div class="muted">by {{ $post->user->name ?? 'Unknown' }} ・ {{ $post->visibility->code ?? '-' }}</div>

      <div style="margin-top:8px; display:flex; gap:8px; align-items:center;">
        <button class="like-btn" data-post-id="{{ $post->id }}" data-url="{{ route('posts.like', $post) }}">いいね</button>
        <span class="muted">Likes: <span class="like-count" data-post-id="{{ $post->id }}">{{ $post->liked_by_users_count ?? 0 }}</span></span>
        <a href="{{ route('posts.show', $post) }}">詳細</a>
      </div>
    </div>
  </article>
@endforeach

<div style="margin-top:16px">
  {{ $posts->links() }}
</div>
@endsection
