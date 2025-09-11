<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            ダッシュボード
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">SNSアプリへようこそ！</h3>
                    <p class="mb-6">ログインしました！以下の機能をお試しください：</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- 投稿 -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-blue-800 mb-2">📝 投稿</h4>
                            <p class="text-sm text-blue-600 mb-3">投稿を表示・作成する</p>
                            <a href="{{ route('posts.index') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                投稿を見る
                            </a>
                        </div>
                        
                        <!-- 投稿作成 -->
                        <div class="bg-green-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-green-800 mb-2">✍️ 作成</h4>
                            <p class="text-sm text-green-600 mb-3">あなたの思いを共有する</p>
                            <a href="{{ route('posts.create') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                新しい投稿
                            </a>
                        </div>
                        
                        <!-- 検索 -->
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-purple-800 mb-2">🔍 検索</h4>
                            <p class="text-sm text-purple-600 mb-3">ユーザーや投稿を探す</p>
                            <a href="{{ route('search') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                検索
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-sm text-gray-600">
                        <p><strong>認証状態:</strong> セッションベース認証が有効です</p>
                        <p><strong>ユーザー:</strong> {{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
