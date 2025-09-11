<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-edit text-white text-lg"></i>
            </div>
            <h2 class="font-bold text-2xl text-white leading-tight">
                新しい投稿を作成
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                    <div class="p-10">
                    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <!-- 投稿内容 -->
                        <div class="space-y-3">
                            <label for="body" class="flex items-center space-x-2 text-lg font-semibold text-gray-800">
                                <i class="fas fa-pen text-blue-500"></i>
                                <span>投稿内容</span>
                            </label>
                            <div class="relative">
                                <textarea id="body" 
                                          name="body" 
                                          rows="8" 
                                          class="w-full rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 shadow-sm text-lg leading-relaxed p-6 transition-all duration-200"
                                          placeholder="今何を考えていますか？あなたの思いを共有しましょう...">{{ old('body') }}</textarea>
                                <div class="absolute bottom-4 right-4 text-sm text-gray-400">
                                    <span id="char-count">0</span> / 1000文字
                                </div>
                            </div>
                            @error('body') 
                                <div class="flex items-center space-x-2 mt-2 text-red-600">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <p class="text-sm">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <!-- 公開範囲 -->
                        <div class="space-y-3">
                            <label for="visibility_id" class="flex items-center space-x-2 text-lg font-semibold text-gray-800">
                                <i class="fas fa-eye text-green-500"></i>
                                <span>公開範囲</span>
                            </label>
                            <select id="visibility_id" 
                                    name="visibility_id" 
                                    class="w-full rounded-xl border-2 border-gray-200 focus:border-green-500 focus:ring-green-500 shadow-sm p-4 text-lg transition-all duration-200">
                                <option value="">公開範囲を選択してください...</option>
                                @foreach ($visibilities as $v)
                                    <option value="{{ $v->id }}" @selected(old('visibility_id') == $v->id)>{{ $v->label }}</option>
                                @endforeach
                            </select>
                            @error('visibility_id') 
                                <div class="flex items-center space-x-2 mt-2 text-red-600">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <p class="text-sm">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        @if(!empty($tags))
                        <!-- タグ -->
                        <div class="space-y-3">
                            <label for="tags" class="flex items-center space-x-2 text-lg font-semibold text-gray-800">
                                <i class="fas fa-tags text-purple-500"></i>
                                <span>タグ</span>
                            </label>
                            @php $oldTags = old('tags', []); @endphp
                            <select id="tags" 
                                    name="tags[]" 
                                    multiple 
                                    size="5"
                                    class="w-full rounded-xl border-2 border-gray-200 focus:border-purple-500 focus:ring-purple-500 shadow-sm p-4 transition-all duration-200">
                                @foreach ($tags as $t)
                                    <option value="{{ $t->id }}" @selected(in_array($t->id, $oldTags))
                                            class="p-2 hover:bg-purple-50">{{ e($t->name) }}</option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 flex items-center space-x-1">
                                <i class="fas fa-info-circle"></i>
                                <span>Ctrl（またはCmd）を押しながらクリックで複数選択できます</span>
                            </p>
                            @error('tags') 
                                <div class="flex items-center space-x-2 mt-2 text-red-600">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <p class="text-sm">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                        @endif

                        <!-- 画像アップロード -->
                        <div class="space-y-3">
                            <label for="image" class="flex items-center space-x-2 text-lg font-semibold text-gray-800">
                                <i class="fas fa-image text-pink-500"></i>
                                <span>画像</span>
                                <span class="text-sm text-gray-500 font-normal">(JPEG/PNG, 最大2MB)</span>
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       id="image" 
                                       name="image" 
                                       accept="image/jpeg,image/png"
                                       class="w-full text-lg text-gray-700 file:mr-4 file:py-4 file:px-6 file:rounded-xl file:border-0 file:text-lg file:font-semibold file:bg-gradient-to-r file:from-pink-50 file:to-purple-50 file:text-pink-700 hover:file:from-pink-100 hover:file:to-purple-100 border-2 border-dashed border-gray-300 rounded-xl p-6 transition-all duration-200 hover:border-pink-400">
                                <div id="image-preview" class="mt-4 hidden">
                                    <img id="preview-img" src="" alt="プレビュー" class="max-w-xs max-h-48 rounded-lg shadow-md">
                                </div>
                            </div>
                            @error('image') 
                                <div class="flex items-center space-x-2 mt-2 text-red-600">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <p class="text-sm">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <!-- アクションボタン -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                            <a href="{{ route('posts.index') }}" 
                               class="flex items-center space-x-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-times"></i>
                                <span>キャンセル</span>
                            </a>
                <button type="submit" 
                    class="flex items-center space-x-2 px-8 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-black font-bold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <i class="fas fa-paper-plane"></i>
                                <span>投稿する</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for enhanced UX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 文字数カウント
            const bodyTextarea = document.getElementById('body');
            const charCount = document.getElementById('char-count');
            
            bodyTextarea.addEventListener('input', function() {
                const count = this.value.length;
                charCount.textContent = count;
                
                if (count > 1000) {
                    charCount.classList.add('text-red-500');
                } else if (count > 800) {
                    charCount.classList.add('text-yellow-500');
                    charCount.classList.remove('text-red-500');
                } else {
                    charCount.classList.remove('text-red-500', 'text-yellow-500');
                }
            });
            
            // 画像プレビュー
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
