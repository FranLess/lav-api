<?php

namespace Tests\Feature\Api\V1;

use App\Events\Models\User\UserCreated;
use App\Events\Models\User\UserDeleted;
use App\Events\Models\User\UserUpdated;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    protected $uri = '/api/v1/users';
    public function test_index()
    {
        // store users to the DB
        $users = User::factory(10)->create();
        $userIds = $users->map(fn ($user) => $user->id);

        // get the response of endpoint
        $response = $this->get($this->uri);

        // assert the response status
        $response->assertStatus(200);

        // get the data from response
        $data = $response->json('data');

        // assert each data gotten with the data stored before
        collect($data)->each(fn ($user) => $this->assertTrue(in_array($user['id'], $userIds->toArray())));
    }

    public function test_show()
    {
        $dummy = User::factory()->create();

        $response = $this->get($this->uri . '/' . $dummy->id);

        $data = $response->assertStatus(200)->json('data');

        $this->assertSame(data_get($data, 'id'), $dummy->id);
    }

    public function test_store()
    {
        Event::fake();
        $dummy = User::factory()->make();

        $dummyPost = Post::factory()->create();

        $response = $this->post($this->uri, array_merge($dummy->toArray(), ['post_ids' => [$dummyPost->id]]));
        $result = $response->assertStatus(201);
        Event::assertDispatched(UserCreated::class);

        $result = collect($result)->only($dummy->getAttributes());

        $result->each(function ($value, $field) use ($dummy) {
            $this->assertSame(data_get($dummy, $field), $value, 'Fillable is not the same');
        });
    }

    public function test_update()
    {
        $dummy = User::factory()->create();
        $dummy2 = User::factory()->make();
        Event::fake();
        $fillables = collect((new User())->getFillable());

        $fillables->each(function ($toUpdate) use ($dummy, $dummy2) {
            $response = $this->json('patch', $this->uri . '/' . $dummy->id, [
                $toUpdate => data_get($dummy2, $toUpdate),
            ]);

            $result = $response->assertStatus(200)->json('data');
            Event::assertDispatched(UserUpdated::class);
            $this->assertEquals(data_get($dummy2, $toUpdate), data_get($dummy->refresh(), $toUpdate), 'Failed to update model.');
        });
    }

    public function test_delete()
    {
        Event::fake();

        $dummy = User::factory()->create();

        $response = $this->delete($this->uri . '/' . $dummy->id);

        $result = $response->assertStatus(200);

        Event::assertDispatched(UserDeleted::class);
        $this->expectException(ModelNotFoundException::class);
        User::findOrFail($dummy->id);
    }
}
