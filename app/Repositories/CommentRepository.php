<?php

namespace App\Repositories;

use App\Events\Models\Comment\CommentCreated;
use App\Events\Models\Comment\CommentDeleted;
use App\Events\Models\Comment\CommentUpdated;
use App\Exceptions\GeneralJsonException;
use App\Models\Comment;
use Illuminate\Support\Facades\DB;

class CommentRepository
{
    public function store(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            $created = Comment::create([
                'body' => data_get($attributes, 'body'),
                'user_id' => data_get($attributes, 'user_id'),
                'post_id' => data_get($attributes, 'post_id')
            ]);


            throw_if((!$created),
                GeneralJsonException::class,
                'Cannot store the comment'
            );

            event(new CommentCreated($created));
            return $created;
        });
    }

    public function update(Comment $comment, array $attributes)
    {
        return DB::transaction(function () use ($attributes, $comment) {
            $updated = $comment->update([
                'body' => data_get($attributes, 'body', $comment->body),
                'user_id' => data_get($attributes, 'user_id', $comment->user_id),
                'post_id' => data_get($attributes, 'post_id', $comment->post_id)
            ]);

            throw_if((!$updated),
                GeneralJsonException::class,
                'Cannot update the comment'
            );

            event(new CommentUpdated($comment));
            return $comment;
        });
    }
    public function delete(Comment $comment)
    {
        return DB::transaction(function () use ($comment) {

            $commentDeleted = Comment::destroy($comment->id);

            throw_if((!$commentDeleted),
                GeneralJsonException::class,
                'Cannot delete the comment'
            );

            event(new CommentDeleted($commentDeleted));
            return $commentDeleted;
        });
    }
}
