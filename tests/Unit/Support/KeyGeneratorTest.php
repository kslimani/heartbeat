<?php

namespace Tests\Unit\Support;

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
        $keys = [];

        for ($i = 0; $i < 10; $i++) {
            $keys[$i] = KeyGenerator::generate();
            $this->assertTrue(mb_strlen($keys[$i]) === 39);
        }

        $this->assertSame($keys, array_unique($keys));
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
            'id' => $authorizedKey->id,
            'user_id' => $user->id,
        ]);
    }
}
