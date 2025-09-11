
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-eye text-white text-lg"></i>
            </div>
            <h2 class="font-bold text-2xl text-white leading-tight">
                投稿詳細
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-6">
                <a href="{{ route('posts.index') }}" 
                   class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 font-semibold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                    <i class="fas fa-arrow-left"></i>
                    <span>投稿一覧に戻る</span>
                </a>
            </div>

            <!-- Post Card -->
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl mb-8 border border-gray-100 hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]">
                <div class="p-6">
                    <!-- Post Header: avatar + username + menu -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-5">
                            <x-user-avatar :user="$post->user" size="sm" class="mr-2 md:mr-3" />
                            <div>
                                @php $visibility = $post->visibility; @endphp

                                <div class="flex items-center">
                                    <h3 class="font-semibold text-gray-900">{{ e($post->user->name) }}</h3>

                                    <span class="text-xs text-gray-500 flex items-center ml-4 space-x-1">
                                        <i class="fas fa-user-friends text-xs"></i>
                                        <span>{{ $post->user->followers_count ?? 0 }}</span>
                                    </span>

                                    <span class="text-xs text-gray-500 flex items-center ml-4 space-x-1">
                                        <i class="{{ $visibility->icon_class }} text-xs"></i>
                                        <span>{{ $visibility->label }}</span>
                                    </span>
                                </div>

                                <p class="text-xs text-gray-500 mt-2">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        @if(auth()->check() && auth()->id() !== $post->user->id)
                            <div class="flex items-center">
                                <button
                                    class="follow-btn inline-flex items-center space-x-2 px-4 py-2 rounded-full border transition-colors duration-150"
                                    data-user-id="{{ $post->user->id }}"
                                    data-url="{{ route('users.follow', $post->user) }}"
                                    data-unfollow-url="{{ route('users.unfollow', $post->user) }}"
                                    data-following="{{ ($isFollowing ?? false) ? '1' : '0' }}"
                                >
                                    <span>{{ ($isFollowing ?? false) ? 'フォロー中' : 'フォロー' }}</span>
                                </button>
                            </div>
                        @else
                            <div class="flex items-center space-x-2">
                                @can('update', $post)
                                    <a href="{{ route('posts.edit', $post) }}" class="inline-flex items-center space-x-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition">
                                        <i class="fas fa-edit"></i>
                                        <span>編集</span>
                                    </a>
                                @endcan

                                @can('delete', $post)
                                    <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('この投稿を削除してもよいですか？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition">
                                            <i class="fas fa-trash"></i>
                                            <span>削除</span>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        @endif
                    </div>

                    <!-- Post Image -->
                    @if($post->attachment)
                        <div class="mb-4">
                            <img src="{{ asset('storage/'.$post->attachment->path) }}" 
                                 alt="投稿画像" 
                                 class="w-full max-w-3xl mx-auto rounded-lg shadow-md border border-gray-200 object-cover">
                        </div>
                    @endif

                    <!-- Post Content -->
                    <div class="mb-6">
                        <div class="prose max-w-none">
                            <p class="text-gray-800 text-lg leading-relaxed whitespace-pre-wrap">{!! nl2br(e($post->body)) !!}</p>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($post->tags->isNotEmpty())
                        <div class="mb-6">
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

                    <!-- Action Bar -->
                    <div class="pt-4">
                        <!-- Likes row -->
                        <div class="mt-3 flex items-center space-x-3">
                            <div class="flex items-center space-x-2">
                                @if(isset($post->liked_by_users) && $post->liked_by_users->isNotEmpty())
                                    <div class="flex -space-x-2">
                                        @foreach($post->liked_by_users->take(5) as $u)
                                            <x-user-avatar :user="$u" size="xs" class="ring-2 ring-white" />
                                        @endforeach
                                    </div>
                                @endif
                                <div class="text text-gray-700">いいね <span class="like-count" data-post-id="{{ $post->id }}">{{ $post->liked_by_users_count ?? 0 }}</span>件</div>
                            </div>

                            @auth
                                @php
                                    $isLiked = auth()->check() ? (bool) auth()->user()->likedPosts()->where('post_id', $post->id)->exists() : false;
                                @endphp
                                <button
                                    type="button"
                                    class="like-btn inline-flex items-center space-x-2 px-3 py-2 rounded-full border transition-colors duration-150"
                                    data-post-id="{{ $post->id }}"
                                    data-url="{{ route('posts.like', $post) }}"
                                >
                                    <i class="fas fa-heart" aria-hidden="true" style="color: {{ $isLiked ? '#ef4444' : '#6b7280' }}"></i>
                                    <span>{{ $isLiked ? 'いいね済み' : 'いいね' }}</span>
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl mb-8 border border-gray-100">
                <div class="p-10">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-400 to-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-comments text-white"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">
                            コメント ({{ $post->comments->count() }})
                        </h3>
                    </div>
                    
                    @forelse($post->comments as $comment)
                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl mb-6 border border-gray-100 hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]">
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    <!-- Comment User Avatar -->
                                    <x-user-avatar :user="$comment->user" size="sm" class="flex-shrink-0" />
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-3">
                                                <h4 class="font-semibold text-gray-800">{{ e($comment->user->name) }}</h4>
                                                <span class="text-sm text-gray-500">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            @can('delete', $comment)
                                                <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-all duration-200" 
                                                            onclick="return confirm('このコメントを削除しますか？')">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{!! \App\Helpers\SecurityHelper::safeBr($comment->body) !!}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-comment text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-lg">まだコメントがありません</p>
                            <p class="text-gray-400">最初のコメントを書いてみましょう！</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Comment Form -->
            @auth
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                    <div class="p-10">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-pen text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">コメントを書く</h3>
                        </div>
                        
                        <form method="POST" action="{{ route('comments.store', $post) }}" class="space-y-4">
                            @csrf
                            <div>
                                <textarea name="body" 
                                          rows="4" 
                                          placeholder="あなたの思いを共有してください..."
                                          class="w-full rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-lg p-4 transition-all duration-200">{{ old('body') }}</textarea>
                                @error('body') 
                                    <div class="flex items-center space-x-2 mt-2 text-red-600">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <p class="text-sm">{{ $message }}</p>
                                    </div>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                <button type="submit" 
                    class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-black font-bold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>コメントを投稿</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 overflow-hidden shadow-xl sm:rounded-2xl border border-blue-200">
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-sign-in-alt text-2xl text-white"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">コメントを投稿するには</h3>
                        <p class="text-gray-600 mb-4">アカウントにログインしてください</p>
                                <a href="{{ route('login') }}" 
                                    class="inline-flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>ログイン</span>
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>

    <!-- JavaScript for enhanced functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Like button functionality
            document.querySelectorAll('.like-btn').forEach(button => {
                // initial state: if server marked as liked (text contains 'いいね済み'), add .liked
                if (button.textContent && button.textContent.trim().includes('いいね済み')) {
                    button.classList.add('liked');
                    button.style.color = '#ef4444';
                    button.style.backgroundColor = '#fff1f2';
                }
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
                                this.style.color = '#ef4444';
                                this.style.backgroundColor = '#fef2f2';
                            } else {
                                this.classList.remove('liked');
                                this.style.color = '#6b7280';
                                this.style.backgroundColor = 'transparent';
                            }
                        }
                    } catch (error) {
                        console.error('エラー:', error);
                    }
                });
            });

            // Follow/Unfollow button functionality
            document.querySelectorAll('.follow-btn').forEach(button => {
                button.addEventListener('click', async function(e) {
                    e.preventDefault();

                    // require auth
                    const csrf = document.querySelector('meta[name="csrf-token"]');
                    if (!csrf) return console.warn('CSRF token not found');

                    const userId = this.dataset.userId;
                    const followUrl = this.dataset.url;
                    const unfollowUrl = this.dataset.unfollowUrl;
                    const isFollowing = this.dataset.following === '1' || this.classList.contains('following');

                    try {
                        const response = await fetch(isFollowing ? unfollowUrl : followUrl, {
                            method: isFollowing ? 'DELETE' : 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf.getAttribute('content'),
                                'Accept': 'application/json',
                            },
                        });

                        if (!response.ok) {
                            const err = await response.json().catch(() => ({}));
                            console.error('Follow action failed', err);
                            return;
                        }

                        const data = await response.json();

                        // Update follower count
                        const countEl = document.querySelector(`.followers-count[data-user-id="${userId}"]`);
                        if (countEl && typeof data.count !== 'undefined') {
                            countEl.textContent = data.count;
                        }

                        // Toggle UI state
                        if (data.following) {
                            this.classList.add('following');
                            this.dataset.following = '1';
                            this.querySelector('span').textContent = 'フォロー中';
                        } else {
                            this.classList.remove('following');
                            this.dataset.following = '0';
                            this.querySelector('span').textContent = 'フォロー';
                        }

                    } catch (error) {
                        console.error('フォロー操作でエラーが発生しました:', error);
                    }
                });
            });

            // Post actions dropdown toggle (ellipsis menu)
            (function() {
                const toggle = document.getElementById('post-actions-toggle');
                const menu = document.getElementById('post-actions-menu');
                if (!toggle || !menu) return;

                const closeMenu = () => {
                    menu.classList.add('hidden');
                    toggle.setAttribute('aria-expanded', 'false');
                };

                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isHidden = menu.classList.contains('hidden');
                    if (isHidden) {
                        menu.classList.remove('hidden');
                        toggle.setAttribute('aria-expanded', 'true');
                    } else {
                        closeMenu();
                    }
                });

                // close on outside click
                document.addEventListener('click', function(ev) {
                    if (!menu.contains(ev.target) && ev.target !== toggle) {
                        closeMenu();
                    }
                });

                // close on escape
                document.addEventListener('keydown', function(ev) {
                    if (ev.key === 'Escape') closeMenu();
                });
            })();
        });
    </script>
</x-app-layout>
