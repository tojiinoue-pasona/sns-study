<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete() // 親ユーザー削除を制限
                ->cascadeOnUpdate();
            $table->foreignId('visibility_id')
                ->constrained('visibilities')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->text('body');
            $table->timestamps();

            // インデックス
            $table->index(['user_id', 'created_at'], 'idx_posts_user_created');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
