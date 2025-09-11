<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('post_id');
            $t->unsignedBigInteger('user_id');
            $t->string('body', 1000);
            $t->timestamps();

            $t->index(['post_id', 'created_at'], 'idx_comments_post_created');
            $t->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $t->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};