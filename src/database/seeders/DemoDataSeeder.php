<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, Tag, Post, Visibility};

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first() ?: User::create([
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        $publicId = Visibility::where('code','public')->value('id');

        $tagA = Tag::firstOrCreate(['name' => 'Laravel']);
        $tagB = Tag::firstOrCreate(['name' => 'PHP']);

        if (!Post::query()->exists() && $publicId) {
            $post = Post::create([
                'user_id' => $user->id,
                'visibility_id' => $publicId,
                'body' => 'はじめての投稿です。',
            ]);
            $post->tags()->sync([$tagA->id, $tagB->id]);
        }
    }
}