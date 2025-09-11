<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-2xl text-white leading-tight flex items-center space-x-2">
                    <i class="fas fa-newspaper"></i>
                    <span>タイムライン</span>
                </h2>
                <p class="text-purple-100 text-sm mt-1">みんなの投稿をチェックしよう</p>
            </div>
            @auth
                     <a href="{{ route('posts.create') }}" 
                         class="btn-primary text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>新規投稿</span>
                </a>
            @endauth
        </div>
    </x-slot>

            <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if($posts->count() > 0)
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
                                    {{-- 右端の重複した可視性表示（薄いグレー）を削除しました。左側の可視性表示はそのまま残します。 --}}
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
                        {{ $posts->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="max-w-md mx-auto">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-comments text-3xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">まだ投稿がありません</h3>
                        <p class="text-gray-600 mb-8 text-lg">最初の投稿をして、コミュニティを盛り上げましょう！</p>
                        @auth
                            <a href="{{ route('posts.create') }}" 
                                class="inline-flex items-center space-x-2 px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <i class="fas fa-plus"></i>
                                <span>最初の投稿を作成</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                                class="inline-flex items-center space-x-2 px-8 py-4 bg-gradient-to-r from-green-500 to-blue-600 text-white font-bold rounded-xl hover:from-green-600 hover:to-blue-700 transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>ログインして投稿</span>
                            </a>
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript for Like functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Like button functionality
            document.querySelectorAll('.like-btn').forEach(button => {
                button.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    const postId = this.dataset.postId;
                    const url = this.dataset.url;
                    const isLiked = this.classList.contains('liked');
                    
                    try {
                        const response = await fetch(url, {
                            method: isLiked ? 'DELETE' : 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            
                            // Update like count
                            const countElement = document.querySelector(`.like-count[data-post-id="${postId}"]`);
                            if (countElement) {
                                countElement.textContent = data.count;
                            }
                            
                            // Toggle button state
                            if (data.liked) {
                                this.classList.add('liked');
                                this.style.color = '#1d4ed8';
                            } else {
                                this.classList.remove('liked');
                                this.style.color = '#6b7280';
                            }
                        } else {
                            console.error('いいねリクエストが失敗しました:', response.statusText);
                        }
                    } catch (error) {
                        console.error('エラー:', error);
                    }
                });
            });
        });
    </script>
</x-app-layout>
