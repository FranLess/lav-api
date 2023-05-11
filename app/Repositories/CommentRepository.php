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
            $comment = Comment::create([
                'title' => data_get($attributes, 'title', 'untittled'),
                'body' => data_get($attributes, 'body')
            ]);

            if ($userIds = data_get($attributes, 'user_ids'))
                $comment->users()->sync($userIds);

            throw_if((!$comment),
                GeneralJsonException::class,
                'Cannot store the comment'
            );

            event(new CommentCreated($comment));
            return $comment;
        });
    }

    public function update(Comment $comment, array $attributes)
    {
        return DB::transaction(function () use ($attributes, $comment) {
            $updated = $comment->update([
                'title' => data_get($attributes, 'title', $comment->title),
                'body' => data_get($attributes, 'body', $comment->body)
            ]);

            if ($userIds = data_get($attributes, 'user_ids'))
                $comment->users()->sync($userIds);

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

            $usersDeleted = $comment->users()->detach();
            $commentdeleted = Comment::destroy($comment->id);

            throw_if((!$commentdeleted || $usersDeleted),
                GeneralJsonException::class,
                'Cannot delete the comment'
            );

            event(new CommentDeleted($commentdeleted));
            return $commentdeleted;
        });
    }
}
