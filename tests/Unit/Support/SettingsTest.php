<?php

namespace Tests\Unit\Support;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Support\Settings;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupApp();
    }

    protected function assertHasDefaultKeys(array $settings)
    {
        $keys = array_keys(Settings::default());

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $settings);
        }
    }

    public function testItGet()
    {
        $user = factory(User::class)->create();

        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $user->id,
        ]);

        $settings = Settings::get($user);

        $this->assertHasDefaultKeys($settings);
        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $user->id,
        ]);
    }

    public function testItSet()
    {
        $user = factory(User::class)->create();

        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $user->id,
        ]);

        $settings = Settings::set($user, ['foo' => 'bar']);

        $this->assertHasDefaultKeys($settings);
        $this->assertArrayHasKey('foo', $settings);
        $this->assertDatabaseHas('user_settings', [
            'user_id' => $user->id,
        ]);
    }

    public function testItGetAuth()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $user->id,
        ]);

        $settings = Settings::getAuth();

        $this->assertHasDefaultKeys($settings);
        $this->assertDatabaseMissing('user_settings', [
            'user_id' => $user->id,
        ]);
    }
}
