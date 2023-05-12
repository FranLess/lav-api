<?php

namespace Tests\Unit;

use App\Events\Models\Post\PostCreated;
use App\Events\Models\Post\PostDeleted;
use App\Events\Models\Post\PostUpdated;
use App\Exceptions\GeneralJsonException;
use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

/** Test case is changed */
class PostRepositoryTest extends TestCase
{
    use RefreshDatabase;
    public function test_store()
    {
        Event::fake();
        // 1. the goal of the test
        // test if store() will actually store a record in DB


        // 2. replicate the env / restriction
        $repository = $this->app->make(PostRepository::class);

        // 3. create the source of truth
        $payload = [
            'title' => 'dummyTitle',
            'body' => []
        ];

        // 4. compare the results
        $result = $repository->store($payload);
        Event::assertDispatched(PostCreated::class);
        $this->assertSame($payload['title'], $result->title, 'Title of post stored and sent are not the same');
    }

    public function test_update()
    {
        Event::fake();
        // test if update() will actually update the record on DB
        $repository = $this->app->make(PostRepository::class);

        $dummy = Post::factory()->create()->first();
        $payload = [
            'title' => 'titleTest',
            'body' => []
        ];

        $repository->update($dummy, $payload);
        Event::assertDispatched(PostUpdated::class);
        $this->assertSame($payload['title'], $dummy->title, 'Post and payload has no the same title');
    }

    public function test_update_body_only()
    {
        // test if update() will actually update the record on DB
        $repository = $this->app->make(PostRepository::class);

        $dummy = Post::factory()->create()->first();
        $payload = [
            'body' => []
        ];

        $repository->update($dummy, $payload);

        $this->assertSame($payload['body'], $dummy->body, 'Post and payload has no the same body');
    }
    public function test_update_title_only()
    {
        // test if update() will actually update the record on DB
        $repository = $this->app->make(PostRepository::class);

        $dummy = Post::factory()->create()->first();
        $payload = [
            'title' => 'titleTest'
        ];

        $repository->update($dummy, $payload);

        $this->assertSame($payload['title'], $dummy->title, 'Post and payload has no the same body');
    }

    public function test_update_will_throw_exception_if_payload_is_empty()
    {
        // test if update() will actually update the record on DB
        $repository = $this->app->make(PostRepository::class);

        $dummy = Post::factory()->create()->first();
        $payload = [];

        $this->expectException(GeneralJsonException::class);
        $repository->update($dummy, $payload);
    }

    public function test_delete()
    {
        Event::fake();
        // test if delete() will actually delete the record on DB
        $repository = $this->app->make(PostRepository::class);

        $dummy = Post::factory()->create()->first();

        $deleted = $repository->delete($dummy);
        Event::assertDispatched(PostDeleted::class);
        $this->assertTrue(boolval($deleted), 'The post was not deleted');
    }
}
