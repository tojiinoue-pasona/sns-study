<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\Visibility;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    /**
     * 学習用デモデータの作成
     */
    public function run(): void
    {
        // 固定ユーザー作成
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $alice = User::firstOrCreate(
            ['email' => 'alice@example.com'],
            [
                'name' => 'Alice Johnson',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $bob = User::firstOrCreate(
            ['email' => 'bob@example.com'],
            [
                'name' => 'Bob Smith',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // 可視性設定取得
        $publicVis = Visibility::where('code', 'public')->first();
        $followersVis = Visibility::where('code', 'Followers')->first();
        $draftVis = Visibility::where('code', 'Draft')->first();

        // タグ作成
        $techTag = Tag::firstOrCreate(['name' => 'Technology']);
        $lifeTag = Tag::firstOrCreate(['name' => 'Life']);
        $workTag = Tag::firstOrCreate(['name' => 'Work']);
        $laravelTag = Tag::firstOrCreate(['name' => 'Laravel']);
        $phpTag = Tag::firstOrCreate(['name' => 'PHP']);

        // 既存投稿をクリア（デモリセット時）
        if (Post::count() > 0) {
            DB::table('likes')->delete();
            DB::table('follows')->delete();
            DB::table('post_tags')->delete();
            Comment::query()->delete();
            Post::query()->delete();
        }

        // Aliceの投稿（各可視性レベル）
        $alicePublicPost = Post::create([
            'user_id' => $alice->id,
            'body' => "こんにちは！新しいプロジェクトを始めました 🚀

LaravelでSNSアプリを作っています。認証、投稿、いいね、フォロー機能を実装中です。

#Laravel #PHP #WebDevelopment",
            'visibility_id' => $publicVis->id,
        ]);
        $alicePublicPost->tags()->attach([$techTag->id, $laravelTag->id]);

        $aliceFollowersPost = Post::create([
            'user_id' => $alice->id,
            'body' => "フォロワー限定投稿です 👥

最近の開発で学んだこと：
- CSRF対策の重要性
- SQL Injectionの防止方法
- XSS対策の実装

詳細は今度まとめてブログに書きます！",
            'visibility_id' => $followersVis->id,
        ]);
        $aliceFollowersPost->tags()->attach([$techTag->id, $workTag->id]);

        $aliceDraftPost = Post::create([
            'user_id' => $alice->id,
            'body' => "これは下書き投稿です 📝

公開前のアイデア：
- セキュリティ監査機能
- パフォーマンス最適化
- UI/UX改善

後で詳細を追加する予定...",
            'visibility_id' => $draftVis->id,
        ]);
        $aliceDraftPost->tags()->attach([$workTag->id]);

        // Bobの投稿（Public）
        $bobPost1 = Post::create([
            'user_id' => $bob->id,
            'body' => "プログラミング学習中です！💻

Laravelの認証機能を理解するのに時間がかかりましたが、Gate/Policyの仕組みが分かってきました。

次はAPI開発に挑戦予定です。",
            'visibility_id' => $publicVis->id,
        ]);
        $bobPost1->tags()->attach([$techTag->id, $laravelTag->id]);

        $bobPost2 = Post::create([
            'user_id' => $bob->id,
            'body' => "今日の学習記録 📚

✅ EloquentのRelationship
✅ Middleware作成
✅ FormRequestバリデーション
✅ セキュリティ対策

少しずつ実装できることが増えてきて楽しいです！",
            'visibility_id' => $publicVis->id,
        ]);
        $bobPost2->tags()->attach([$techTag->id, $lifeTag->id, $phpTag->id]);

        // BobからAliceの投稿へのコメント
        Comment::create([
            'user_id' => $bob->id,
            'post_id' => $alicePublicPost->id,
            'body' => "素晴らしいプロジェクトですね！🎉

LaravelのSNS機能実装、参考にさせていただきます。特にセキュリティ対策の部分が勉強になります。",
        ]);

        Comment::create([
            'user_id' => $alice->id,
            'post_id' => $bobPost1->id,
            'body' => "Gate/Policyは最初は複雑に感じますが、理解すると強力な機能ですよね！API開発も頑張ってください 💪",
        ]);

        // いいね機能（BobがAliceの投稿にいいね）
        DB::table('likes')->insert([
            'user_id' => $bob->id,
            'post_id' => $alicePublicPost->id,
            'created_at' => now(),
        ]);

        DB::table('likes')->insert([
            'user_id' => $alice->id,
            'post_id' => $bobPost2->id,
            'created_at' => now(),
        ]);

        // フォロー関係（BobがAliceをフォロー）
        DB::table('follows')->insert([
            'follower_id' => $bob->id,
            'followee_id' => $alice->id,
            'created_at' => now(),
        ]);

        echo "✅ デモデータ作成完了:
";
        echo "   - Admin: admin@example.com
";
        echo "   - Alice: alice@example.com (3投稿: Public/Followers/Draft)
";
        echo "   - Bob: bob@example.com (2投稿, Alice follow済み)
";
    }
}