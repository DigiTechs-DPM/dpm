<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\Brand;
use App\Models\Seller;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeadStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_can_be_stored_via_api()
    {
        // 1️⃣ Create a fake brand and seller
        $brand = Brand::factory()->create(['brand_name' => 'Demo LLC']);
        $seller = Seller::factory()->create(['brand_id' => $brand->id]);

        // 2️⃣ Mock LeadAssigner to always return our test seller
        $assignerMock = Mockery::mock(\App\Services\LeadAssigner::class);
        $assignerMock->shouldReceive('assignNext')->andReturn($seller);
        App::instance(\App\Services\LeadAssigner::class, $assignerMock);

        // 3️⃣ Mock LeadClassifier to return a dummy prediction
        $classifierMock = Mockery::mock(\App\Services\LeadClassifier::class);
        $classifierMock->shouldReceive('classify')->andReturn([
            'status' => 'real',
            'score'  => 90,
        ]);
        App::instance(\App\Services\LeadClassifier::class, $classifierMock);

        // 4️⃣ Prepare request payload
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'service' => 'Web Design',
            'message' => 'Need a landing page',
            'url' => 'https://example.com',
            'utm_source' => 'google',
            'timezone' => 'UTC',
        ];

        // 5️⃣ Send POST request to API route
        $response = $this->postJson('/api/leads', $payload);

        // 6️⃣ Assert success + structure
        $response->assertStatus(201)
            ->assertJson([
                'ok' => true,
                'data' => [
                    'email' => 'john@example.com',
                    'status' => 'new',
                ],
            ]);

        // 7️⃣ Validate database records
        $this->assertDatabaseHas('leads', [
            'email' => 'john@example.com',
            'brand_id' => $brand->id,
            'seller_id' => $seller->id,
            'status' => 'new',
        ]);

        // Check client creation
        $this->assertDatabaseHas('clients', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);
    }
}
