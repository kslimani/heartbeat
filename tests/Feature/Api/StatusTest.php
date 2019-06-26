<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Support\KeyGenerator;

class StatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupApp();
    }

    public function testItCheck()
    {
        $user = factory(User::class)->create();
        $key = KeyGenerator::make($user);

        $response = $this->json('POST', '/api/status/check', [
            'key' => $key->data,
        ]);

        $response->assertStatus(200);

        $response = $this->json('POST', '/api/status/check', [
            'key' => 'bad-length',
        ]);

        $response->assertStatus(403);

        $response = $this->json('POST', '/api/status/check', [
            'key' => 'does-not-exists-in-database-39-str-size',
        ]);

        $response->assertStatus(403);
    }

    public function testItHandleServiceStatus()
    {
        $user = factory(User::class)->create();
        $key = KeyGenerator::make($user);

        $response = $this->json('POST', '/api/status', [
            'key' => $key->data,
            'device' => 'my.device',
            'service' => 'acme-srv',
            'status' => 'UP',
        ]);

        $response->assertStatus(200);

        $response = $this->json('POST', '/api/status', [
            'key' => $key->data,
            'device' => 'my.device',
            'service' => 'bad@name',
            'status' => 'UP',
        ]);

        $response->assertStatus(422);

        $response = $this->json('POST', '/api/status', [
            'key' => $key->data,
            'device' => 'my.device',
            'service' => 'acme-srv',
            'status' => 'UNKNOW_STATUS',
        ]);

        $response->assertStatus(400);

        $response = $this->json('POST', '/api/status', [
            'key' => 'bad.key',
            'device' => 'my.device',
            'service' => 'acme-srv',
            'status' => 'UP',
        ]);

        $response->assertStatus(403);
    }
}
