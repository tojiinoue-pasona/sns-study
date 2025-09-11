<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            投稿を編集
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Post Content -->
                        <div class="mb-6">
                            <x-input-label for="body" :value="__('本文')" />
                            <textarea id="body" 
                                      name="body" 
                                      rows="6"
                                      class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                      placeholder="投稿内容を入力してください...">{{ old('body', $post->body) }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        </div>

                        <!-- Visibility -->
                        <div class="mb-6">
                            <x-input-label for="visibility_id" :value="__('公開範囲')" />
                            <select id="visibility_id" 
                                    name="visibility_id"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($visibilities as $v)
                                    <option value="{{ $v->id }}" @selected(old('visibility_id', $post->visibility_id) == $v->id)>
                                        {{ $v->label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('visibility_id')" class="mt-2" />
                        </div>

                        <!-- Tags -->
                        @if(!empty($tags))
                        <div class="mb-6">
                            <x-input-label for="tags" :value="__('タグ')" />
                            @php
                                $selected = old('tags', $post->relationLoaded('tags') ? $post->tags->pluck('id')->all() : $post->tags()->pluck('id')->all());
                            @endphp
                            <select id="tags" 
                                    name="tags[]" 
                                    multiple 
                                    size="5"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($tags as $t)
                                    <option value="{{ $t->id }}" @selected(in_array($t->id, $selected))>
                                        {{ e($t->name) }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-600">Ctrlキー（Macの場合はCmdキー）を押しながらクリックして複数選択</p>
                            <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                            <x-input-error :messages="$errors->get('tags.*')" class="mt-2" />
                        </div>
                        @endif

                        <!-- Image Upload -->
                        <div class="mb-6">
                            <x-input-label for="image" :value="__('画像（差し替え）')" />
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   accept="image/jpeg,image/png"
                                   class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <p class="mt-1 text-sm text-gray-600">JPEG, PNG形式のみ対応</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />

                            @if($post->attachment)
                                <div class="mt-4">
                                    <p class="text-sm text-gray-700 mb-2">現在の画像:</p>
                                    <img src="{{ asset('storage/'.$post->attachment->path) }}" 
                                         alt="Current image" 
                                         class="w-40 h-40 object-cover border border-gray-300 rounded-lg">
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-4">
                                     <a href="{{ route('posts.show', $post) }}" 
                                         class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('キャンセル') }}
                            </a>
                            
                            <x-primary-button>
                                {{ __('更新') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
