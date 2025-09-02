<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostAttachment;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function create(array $data, ?UploadedFile $image, Request $request): Post
    {
        $post = Post::create($data);

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        if ($image) {
            $this->storeAttachment($post, $image, $request);
        }

        return $post;
    }

    public function update(Post $post, array $data, ?UploadedFile $image, Request $request): Post
    {
        // tags は別管理
        $tags = $data['tags'] ?? null;
        unset($data['tags']);

        if (!empty($data)) {
            $post->fill($data)->save();
        }

        if (is_array($tags)) {
            $post->tags()->sync($tags);
        }

        if ($image) {
            $this->replaceAttachment($post, $image, $request);
        }

        return $post;
    }

    private function storeAttachment(Post $post, UploadedFile $file, Request $request): void
    {
        $path = $file->store('attachments', 'public');
        $post->attachment()->create([
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'size' => (int) $file->getSize(),
        ]);

        \App\Services\AuditLogger::uploadSucceeded(
            $post->user_id, $post->id, $path, (string)$file->getClientMimeType(), (int)$file->getSize(), $request
        );
    }

    private function replaceAttachment(Post $post, UploadedFile $file, Request $request): void
    {
        if ($post->attachment && $post->attachment->path) {
            Storage::disk('public')->delete($post->attachment->path);
        }

        $path = $file->store('attachments', 'public');
        $post->attachment()->updateOrCreate(
            [],
            ['path' => $path, 'mime' => $file->getClientMimeType(), 'size' => (int)$file->getSize()]
        );

        \App\Services\AuditLogger::uploadSucceeded(
            $post->user_id, $post->id, $path, (string)$file->getClientMimeType(), (int)$file->getSize(), $request
        );
    }
}