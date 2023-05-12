<?php

namespace App\Repositories;

use App\Events\Models\Post\PostCreated;
use App\Events\Models\Post\PostDeleted;
use App\Events\Models\Post\PostUpdated;
use App\Events\Models\User\UserUpdated;
use App\Events\Models\UserCreated;
use App\Exceptions\GeneralJsonException;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostRepository
{
    public function store(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            $created = Post::create([
                'title' => data_get($attributes, 'title', 'untittled'),
                'body' => data_get($attributes, 'body')
            ]);

            if ($userIds = data_get($attributes, 'user_ids'))
                $created->users()->sync($userIds);

            throw_if((!$created),
                GeneralJsonException::class,
                'Cannot store the post'
            );

            event(new PostCreated($created));
            return $created;
        });
    }

    public function update(Post $post, array $attributes)
    {
        return DB::transaction(function () use ($attributes, $post) {
            $updated = $post->update([
                'title' => data_get($attributes, 'title', $post->title),
                'body' => data_get($attributes, 'body', $post->body)
            ]);

            if ($userIds = data_get($attributes, 'user_ids'))
                $post->users()->sync($userIds);

            throw_if((!$updated),
                GeneralJsonException::class,
                'Cannot update the post'
            );

            event(new PostUpdated($post));
            return $post;
        });
    }
    public function delete(Post $post)
    {
        return DB::transaction(function () use ($post) {

            $usersDeleted = $post->users()->detach();
            $postDeleted = Post::destroy($post->id);

            throw_if((!$postDeleted || $usersDeleted),
                GeneralJsonException::class,
                'Cannot delete the post'
            );

            event(new PostDeleted($postDeleted));
            return $postDeleted;
        });
    }
}
