@extends('layouts.app')

@php use Illuminate\Support\Str; @endphp

@section('content')
<h1>検索</h1>

<form method="GET" action="{{ route('search.index') }}" class="card" style="display:flex; gap:12px; align-items:flex-end;">
  <div class="field" style="flex:1;">
    <label for="q">キーワード</label><br>
    <input id="q" type="text" name="q" value="{{ old('q', $q) }}" placeholder="本文を検索">
    @error('q') <div class="error-text">{{ $message }}</div> @enderror
  </div>

  <div class="field">
    <label for="tag">タグ</label><br>
    <select id="tag" name="tag">
      <option value="">すべて</option>
      @foreach($tags as $t)
        <option value="{{ $t->id }}" @selected(old('tag', $tag)==$t->id)>{{ $t->name }}</option>
      @endforeach
    </select>
    @error('tag') <div class="error-text">{{ $message }}</div> @enderror
  </div>

  <div class="field">
    <button type="submit">検索</button>
  </div>
</form>

@if($posts->count() === 0)
  <div class="muted">該当する投稿がありません。</div>
@endif

@foreach ($posts as $post)
  <article class="card" style="display:flex;gap:12px;align-items:flex-start;">
    @if($post->attachment)
      <img src="{{ asset('storage/'.$post->attachment->path) }}" alt="" style="width:96px;height:96px;object-fit:cover;border:1px solid #ddd;border-radius:8px;">
    @endif
    <div style="flex:1">
      <p>{{ Str::limit($post->body, 160) }}</p>
      <div class="muted">by {{ $post->user->name ?? 'Unknown' }} ・ {{ $post->visibility->code ?? '-' }}</div>
      <div style="margin-top:8px">
        <a href="{{ route('posts.show', $post) }}">詳細</a>
      </div>
    </div>
  </article>
@endforeach

<div style="margin-top:16px">
  {{ $posts->links() }}
</div>
@endsection