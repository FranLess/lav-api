<?php

namespace Tests\Feature\Api\V1;

use App\Events\Models\Comment\CommentCreated;
use App\Events\Models\Comment\CommentDeleted;
use App\Events\Models\Comment\CommentUpdated;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;
    protected $uri = '/api/v1/comments';
    public function test_index()
    {
        $comments = Comment::factory(10)->create();
        $commentIds = $comments->map(fn ($comment) => $comment->id);

        $response = $this->get($this->uri);

        $response->assertStatus(200);

        $data = $response->json('data');

        collect($data)->each(fn ($comment) => $this->assertTrue(in_array($comment['id'], $commentIds->toArray())));
    }

    public function test_show()
    {
        $dummy = Comment::factory()->create();

        $response = $this->get($this->uri . '/' . $dummy->id);

        $data = $response->assertStatus(200)->json('data');

        $this->assertSame(data_get($data, 'id'), $dummy->id);
    }

    public function test_store()
    {
        Event::fake();
        $dummy = Comment::factory()->make();


        $response = $this->post($this->uri, $dummy->toArray());
        $result = $response->assertStatus(201)->json('data');

        Event::assertDispatched(CommentCreated::class);
        $result = collect($result)->only(array_keys($dummy->getAttributes()));

        $result->each(function ($value, $field) use ($dummy) {
            $this->assertSame(data_get($dummy, $field), $value, 'Fillable are not the same');
        });
    }

    public function test_update()
    {
        Event::fake();
        $dummy1 = Comment::factory()->create();
        $dummy2 = Comment::factory()->make();

        $fillables = collect((new Comment)->getFillable());
        $fillables->each(function ($toUpdate) use ($dummy1, $dummy2) {
            $response = $this->patch($this->uri . '/' . $dummy1->id, [
                $toUpdate => data_get($dummy2, $toUpdate),
            ]);

            $response->assertStatus(200);
            Event::assertDispatched(CommentUpdated::class);
            $this->assertEquals(data_get($dummy2, $toUpdate), data_get($dummy1->refresh(), $toUpdate), 'Failed to upload the model');
        });
    }

    public function test_delete()
    {
        Event::fake();
        $dummy = Comment::factory()->create();

        $response = $this->delete($this->uri . '/' . $dummy->id);

        $result = $response->assertStatus(200);
        Event::assertDispatched(CommentDeleted::class);
        $this->expectException(ModelNotFoundException::class);
        Comment::findOrFail($dummy->id);
    }
}
