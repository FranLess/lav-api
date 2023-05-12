<?php

namespace App\Http\Controllers;

use App\Events\Models\User\UserCreated;
use App\Exceptions\GeneralJsonException;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // event(new UserCreated(User::factory()->create()));
        $posts = Post::paginate(20);

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request, PostRepository $postRepository)
    {
        $created = $postRepository->store($request->only([
            'title',
            'body',
            'user_ids'
        ]));

        return new PostResource($created);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post, PostRepository $postRepository)
    {
        $post = $postRepository->update($post, $request->only([
            'title',
            'body',
            'user_ids'
        ]));

        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, PostRepository $postRepository)
    {
        $postRepository->delete($post);
        return new JsonResponse([
            'data' => 'succes'
        ]);
    }
}
