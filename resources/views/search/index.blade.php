<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-search text-white text-lg"></i>
            </div>
            <h2 class="font-bold text-2xl text-white leading-tight">
                投稿を検索
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl mb-8 border border-gray-100">
                <div class="p-6 md:p-8">
                    <form method="GET" action="{{ route('search') }}" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                            <!-- キーワード検索 -->
                            <div class="space-y-2">
                                <label for="q" class="flex items-center space-x-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-pen text-purple-500 text-sm"></i>
                                    <span>キーワード</span>
                                </label>
                                <input id="q" 
                                       type="text" 
                                       name="q" 
                                       value="{{ old('q', $q ?? '') }}" 
                                       placeholder="投稿内容を検索..."
                                       class="w-full rounded-lg border border-gray-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-100 shadow-sm text-base p-3 transition-all duration-200 placeholder:text-gray-400">
                                @error('q') 
                                    <div class="flex items-center space-x-2 mt-2 text-red-600">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <p class="text-sm">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>

                            <!-- タグ検索 -->
                            <div class="space-y-2">
                                <label for="tag" class="flex items-center space-x-2 text-sm font-medium text-gray-700">
                                    <i class="fas fa-tags text-pink-500 text-sm"></i>
                                    <span>タグ</span>
                                </label>
                                <select id="tag" 
                                        name="tag"
                                        class="w-full rounded-lg border border-gray-200 focus:border-pink-400 focus:ring-2 focus:ring-pink-100 shadow-sm text-base p-3 transition-all duration-200">
                                    <option value="">すべてのタグ</option>
                                    @if(isset($tags))
                                        @foreach($tags as $t)
                                            <option value="{{ $t->id }}" @selected(old('tag', $tag ?? '')==$t->id)>{{ e($t->name) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('tag') 
                                    <div class="flex items-center space-x-2 mt-2 text-red-600">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <p class="text-sm">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>

                            <!-- 検索ボタン -->
                            <div class="flex items-end gap-3">
                                <button type="submit" 
                                    class="w-full flex items-center justify-center space-x-2 px-5 py-3 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-black font-semibold rounded-lg transition-all duration-200 shadow-md text-base">
                                    <i class="fas fa-search text-sm"></i>
                                    <span>検索</span>
                                </button>
                                @if(!empty($q) || !empty($tag))
                                    <a href="{{ route('search') }}" class="hidden md:inline-flex items-center px-4 py-3 text-sm text-gray-600 hover:text-gray-800 rounded-lg hover:bg-gray-50 border border-transparent">
                                        クリア
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            @if(isset($posts) && $posts->count() > 0)
                <div class="space-y-6">
                    <!-- Results Header -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-list text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">
                                検索結果 <span class="text-purple-600">({{ $posts->total() }}件)</span>
                            </h3>
                        </div>
                    </div>
                    
                    <!-- Results List -->
                    <div class="space-y-6">
                        @foreach($posts as $post)
                            <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100 hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]">
                                        <div class="p-6">
                                    <!-- Post Header -->
                                    <div class="flex items-center space-x-5 mb-4">
                                        <x-user-avatar :user="$post->user" size="sm" class="mr-2 md:mr-3" />
                                        <div>
                                            @php $visibility = $post->visibility; @endphp
                                            <div class="flex items-center">
                                                <h4 class="font-bold text-lg text-gray-800">{{ e($post->user->name ?? 'Unknown') }}</h4>
                                                <span class="text-sm text-gray-500 flex items-center ml-4 space-x-1">
                                                    <i class="fas fa-user-friends text-xs"></i>
                                                    <span class="text-xs">{{ $post->user->followers_count ?? 0 }}</span>
                                                </span>
                                                <span class="text-sm text-gray-500 flex items-center ml-4 space-x-1">
                                                    <i class="{{ $visibility->icon_class }} text-xs"></i>
                                                    <span class="text-xs">{{ $visibility->label }}</span>
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-2">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $post->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Post Image -->
                                    @if($post->attachment)
                                        <div class="mb-4">
                                            <img src="{{ asset('storage/'.$post->attachment->path) }}" 
                                                 alt="投稿画像" 
                                                 class="w-32 h-32 object-cover rounded-xl shadow-md border border-gray-200">
                                        </div>
                                    @endif
                                    
                                    <!-- Post Content -->
                                    <div class="mb-4">
                                        <p class="text-gray-800 text-lg leading-relaxed">{{ \App\Helpers\SecurityHelper::safeLimit($post->body ?? $post->content ?? '', 200) }}</p>
                                    </div>
                                    
                                    <!-- Tags -->
                                    @if($post->tags->isNotEmpty())
                                        <div class="mb-4">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($post->tags as $tag)
                                                    <span class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-blue-100 to-purple-100 text-blue-800 text-sm font-medium rounded-full">
                                                        <i class="fas fa-tag mr-1 text-xs"></i>
                                                        {{ e($tag->name) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Action Button -->
                                    <div class="flex justify-end">
                                        <a href="{{ route('posts.show', $post) }}" 
                                           class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-black font-semibold rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                                            <i class="fas fa-eye"></i>
                                            <span>詳細を見る</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center">
                        <div class="pagination-container">
                            {{ $posts->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @elseif(isset($posts))
                <!-- No Results -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                    <div class="text-center py-16">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-search text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">検索結果が見つかりません</h3>
                        <p class="text-gray-600 mb-6 text-lg">検索条件を変更して再度お試しください</p>
                        <div class="space-y-2 text-sm text-gray-500">
                            <p><i class="fas fa-lightbulb mr-2"></i>異なるキーワードを試してみてください</p>
                            <p><i class="fas fa-tags mr-2"></i>タグフィルターを変更してみてください</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Initial State -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                    <div class="text-center py-16">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-search text-3xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">投稿を検索</h3>
                        <p class="text-gray-600 text-lg mb-6">キーワードやタグを入力して投稿を探しましょう</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto text-sm text-gray-500">
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fas fa-pen text-purple-500"></i>
                                <span>キーワード検索</span>
                            </div>
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fas fa-tags text-pink-500"></i>
                                <span>タグ絞り込み</span>
                            </div>
                            <div class="flex items-center justify-center space-x-2">
                                <i class="fas fa-filter text-blue-500"></i>
                                <span>条件を組み合わせ</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
