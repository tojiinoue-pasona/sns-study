<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Comment;
use App\Policies\PostPolicy;
use App\Policies\CommentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        Comment::class => CommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // 管理者用Gate
        Gate::define('admin-only', function ($user) {
            return $user->isAdmin();
        });

        // フォロー関連Gate（将来的に使用可能）
        Gate::define('follow-user', function ($user, $targetUser) {
            return $user->id !== $targetUser->id; // 自分自身はフォローできない
        });
    }
}
