<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Support\KeyGenerator;

class StatusTest extends TestCase
{
    use RefreshDatabase;

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
}
