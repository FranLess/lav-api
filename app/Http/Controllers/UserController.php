<?php

namespace App\Http\Controllers;

use App\Events\Models\User\UserCreated;
use App\Exceptions\GeneralJsonException;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // event(new UserCreated(User::factory()->create()));
        $user = User::paginate(20)->with(['posts']);

        return UserResource::collection($user);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request, UserRepository $userRepository)
    {
        $created = $userRepository->store($request->only([
            'name',
            'email',
            'users_ids'
        ]));

        return new UserResource($created);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UserRepository $userRepository)
    {
        $userRepository->update($user, $request->only([
            'name',
            'email',
            'users_ids'
        ]));

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, UserRepository $userRepository)
    {
        $userRepository->delete($user);
        return new JsonResponse([
            'data' => 'succes'
        ]);
    }
}
