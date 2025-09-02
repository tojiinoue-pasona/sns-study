@extends('layouts.app')

@section('content')
<h1>Edit Post</h1>

<form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="field">
    <label for="body">本文</label><br>
    <textarea id="body" name="body" rows="6">{{ old('body', $post->body) }}</textarea>
    @error('body') <div class="error-text">{{ $message }}</div> @enderror
  </div>

  <div class="field">
    <label for="visibility_id">公開範囲</label><br>
    <select id="visibility_id" name="visibility_id">
      @foreach ($visibilities as $v)
        <option value="{{ $v->id }}" @selected(old('visibility_id', $post->visibility_id) == $v->id)>{{ $v->code }}</option>
      @endforeach
    </select>
    @error('visibility_id') <div class="error-text">{{ $message }}</div> @enderror
  </div>

  @if(!empty($tags))
  <div class="field">
    <label for="tags">タグ</label><br>
    @php
      $selected = old('tags', $post->relationLoaded('tags') ? $post->tags->pluck('id')->all() : $post->tags()->pluck('id')->all());
    @endphp
    <select id="tags" name="tags[]" multiple size="5">
      @foreach ($tags as $t)
        <option value="{{ $t->id }}" @selected(in_array($t->id, $selected))>{{ $t->name }}</option>
      @endforeach
    </select>
    @error('tags') <div class="error-text">{{ $message }}</div> @enderror
    @error('tags.*') <div class="error-text">{{ $message }}</div> @enderror
  </div>
  @endif

  <div class="field">
    <label for="image">画像（差し替え）</label><br>
    <input type="file" id="image" name="image" accept="image/jpeg,image/png">
    @error('image') <div class="error-text">{{ $message }}</div> @enderror

    @if($post->attachment)
      <div style="margin-top:8px">
        <img src="{{ asset('storage/'.$post->attachment->path) }}" alt="" style="width:160px;height:160px;object-fit:cover;border:1px solid #ddd;border-radius:8px;">
      </div>
    @endif
  </div>

  <button type="submit">更新</button>
  <a href="{{ route('posts.show', $post) }}">戻る</a>
</form>
@endsection