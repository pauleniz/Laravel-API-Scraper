<?php

namespace API\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class JobApiTest extends TestCase
{
    use RefreshDatabase;

    public function testJobCreation()
    {
        Redis::flushdb();

        Queue::fake();

        $user = User::factory()->withApiToken()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/v1/jobs', [
            'urls' => ['https://example.com'],
            'selectors' => ['h1'],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['job_id']);
    }

    public function testJobRetrieval()
    {
        $user = User::factory()->withApiToken()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/v1/jobs', [
            'urls' => ['https://example.com'],
            'selectors' => ['h1'],
        ]);

        $jobId = $response->json('job_id');

        $getResponse = $this->getJson("/api/v1/jobs/$jobId");

        $getResponse->assertStatus(200)
            ->assertJsonStructure(['id', 'status', 'urls', 'selectors', 'data']);
    }

    public function testJobDeletion()
    {
        Queue::fake();

        $user = User::factory()->withApiToken()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/v1/jobs', [
            'urls' => ['https://example.com'],
            'selectors' => ['h1'],
        ]);

        $jobId = $response->json('job_id');

        $deleteResponse = $this->deleteJson("/api/v1/jobs/$jobId");

        $deleteResponse->assertStatus(200)
            ->assertJson(['message' => 'Job deleted']);

        $getResponse = $this->getJson("/api/v1/jobs/$jobId");

        $getResponse->assertStatus(404);
    }

    public function testJobCreationWithInvalidUrl()
    {
        $user = User::factory()->withApiToken()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/v1/jobs', [
            'urls' => ['invalid-url'], // Invalid URL format
            'selectors' => ['h1'],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['urls.0']);
    }

    public function testJobCreationWithMissingData()
    {
        $user = User::factory()->withApiToken()->create();
        $this->actingAs($user, 'sanctum');

        // Try creating a job without providing 'urls'
        $response = $this->postJson('/api/v1/jobs', [
            'selectors' => ['h1'],
        ]);

        $response->assertStatus(422) // Unprocessable Entity
        ->assertJsonValidationErrors(['urls']);
    }

    public function testJobCreationWithInvalidSelectors()
    {
        $user = User::factory()->withApiToken()->create();
        $this->actingAs($user, 'sanctum');

        // Create a job with invalid selectors (not a valid CSS selector)
        $response = $this->postJson('/api/v1/jobs', [
            'urls' => ['https://example.com'],
            'selectors' => ['invalid selector<>+'], // Invalid CSS selector
        ]);

        $response->assertStatus(422); // Assuming you have validation for selectors
    }

    public function testUnauthorizedJobCreation()
    {
        // Don't authenticate a user
        $response = $this->postJson('/api/v1/jobs', [
            'urls' => ['https://example.com'],
            'selectors' => ['h1'],
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    public function testDeletingNonExistentJob()
    {
        $user = User::factory()->withApiToken()->create();
        $this->actingAs($user, 'sanctum');

        // Try deleting a job that does not exist
        $response = $this->deleteJson('/api/v1/jobs/9999999');

        $response->assertStatus(404); // Not Found
    }

    public function testConcurrentJobCreation()
    {
        Queue::fake();

        $user = User::factory()->withApiToken()->create();
        $this->actingAs($user, 'sanctum');

        // Simulate concurrent job creation
        $responses = [];
        for ($i = 0; $i < 10; $i++) {
            $responses[] = $this->postJson('/api/v1/jobs', [
                'urls' => ['https://example.com'],
                'selectors' => ['h1'],
            ]);
        }

        foreach ($responses as $response) {
            $response->assertStatus(201)
                ->assertJsonStructure(['job_id']);
        }
    }

    public function testJobDeletionAndCleanup()
    {
        Queue::fake();

        $user = User::factory()->withApiToken()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/v1/jobs', [
            'urls' => ['https://example.com'],
            'selectors' => ['h1'],
        ]);

        $jobId = $response->json('job_id');

        // Delete the job
        $this->deleteJson("/api/v1/jobs/$jobId")->assertStatus(200);

        // Verify the job no longer exists
        $this->getJson("/api/v1/jobs/$jobId")->assertStatus(404);
    }
}
