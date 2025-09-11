<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('post_attachments', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->unsignedBigInteger('post_id');
            $t->string('path', 255);
            $t->string('mime', 64);
            $t->unsignedInteger('size');
            $t->timestamps();

            $t->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $t->index('post_id', 'idx_post_attachments_post');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_attachments');
    }
};