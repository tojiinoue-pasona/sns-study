@extends('layouts.app')

@section('content')
<h1>Create Post</h1>

<form method="POST" action="{{ route('posts.store') }}">
  @csrf

  <div class="field">
    <label for="body">本文</label><br>
    <textarea id="body" name="body" rows="6">{{ old('body') }}</textarea>
    @error('body') <div class="error-text">{{ $message }}</div> @enderror
  </div>

  <div class="field">
    <label for="visibility_id">公開範囲</label><br>
    <select id="visibility_id" name="visibility_id">
      <option value="">選択してください</option>
      @foreach ($visibilities as $v)
        <option value="{{ $v->id }}" @selected(old('visibility_id') == $v->id)>{{ $v->code }}</option>
      @endforeach
    </select>
    @error('visibility_id') <div class="error-text">{{ $message }}</div> @enderror
  </div>

  @if(!empty($tags))
  <div class="field">
    <label for="tags">タグ</label><br>
    @php $oldTags = old('tags', []); @endphp
    <select id="tags" name="tags[]" multiple size="5">
      @foreach ($tags as $t)
        <option value="{{ $t->id }}" @selected(in_array($t->id, $oldTags))>{{ $t->name }}</option>
      @endforeach
    </select>
    @error('tags') <div class="error-text">{{ $message }}</div> @enderror
    @error('tags.*') <div class="error-text">{{ $message }}</div> @enderror
  </div>
  @endif

  <button type="submit">保存</button>
  <a href="{{ route('posts.index') }}">キャンセル</a>
</form>
@endsection