<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $t) {
            $t->unsignedBigInteger('follower_id');
            $t->unsignedBigInteger('followee_id');
            $t->timestamp('created_at')->useCurrent();

            $t->primary(['follower_id','followee_id'], 'pk_follows');
            $t->index('followee_id', 'idx_follows_followee');

            $t->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            $t->foreign('followee_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};