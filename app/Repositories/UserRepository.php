<?php

namespace App\Repositories;

use App\Events\Models\User\UserCreated;
use App\Events\Models\User\UserDeleted;
use App\Events\Models\User\UserUpdated;
use App\Exceptions\GeneralJsonException;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function store(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            $user = User::create([
                'title' => data_get($attributes, 'title', 'untittled'),
                'body' => data_get($attributes, 'body')
            ]);

            if ($postIds = data_get($attributes, 'user_ids'))
                $user->posts()->sync($postIds);

            throw_if((!$user),
                GeneralJsonException::class,
                'Cannot store the user'
            );

            event(new UserCreated($user));
            return $user;
        });
    }

    public function update(User $user, array $attributes)
    {
        return DB::transaction(function () use ($attributes, $user) {
            $updated = $user->update([
                'title' => data_get($attributes, 'title', $user->title),
                'body' => data_get($attributes, 'body', $user->body)
            ]);

            if ($postIds = data_get($attributes, 'user_ids'))
                $user->posts()->sync($postIds);

            throw_if((!$updated),
                GeneralJsonException::class,
                'Cannot update the user'
            );

            event(new UserUpdated($user));
            return $user;
        });
    }
    public function delete(User $user)
    {
        return DB::transaction(function () use ($user) {

            $postsDeleted = $user->posts()->detach();
            $userDeleted = User::destroy($user->id);

            throw_if((!$userDeleted || $postsDeleted),
                GeneralJsonException::class,
                'Cannot delete the user'
            );

            event(new UserDeleted($userDeleted));
            return $userDeleted;
        });
    }
}
