@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('content')
<h1>Posts</h1>

@foreach ($posts as $post)
  <article class="card">
    <p>{{ Str::limit($post->body, 140) }}</p>
    <div class="muted">
      by {{ $post->user->name }} ・ visibility: {{ $post->visibility->code }}
    </div>
    <div style="margin-top:8px">
      <a href="{{ route('posts.show', $post) }}">詳細を見る →</a>
    </div>
  </article>
@endforeach

<div style="margin-top:16px">
  {{ $posts->links() }}
</div>
@endsection
