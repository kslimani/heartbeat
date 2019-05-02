<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\AuthorizedKey;
use App\User;
use App\Support\KeyGenerator;

class KeyGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function testItGenerate()
    {
        $key = KeyGenerator::generate();

        $this->assertTrue(mb_strlen($key) === 39);
    }

    public function testItMake()
    {
        $user = factory(User::class)->create();

        $this->assertDatabaseMissing('authorized_keys', [
            'user_id' => $user->id,
        ]);

        $authorizedKey = KeyGenerator::make($user);

        $this->assertInstanceOf(AuthorizedKey::class, $authorizedKey);
        $this->assertDatabaseHas('authorized_keys', [
            'user_id' => $user->id,
        ]);
    }
}
