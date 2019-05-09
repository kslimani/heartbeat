<?php

namespace Tests\Unit\Support;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use App\Role;
use App\ServiceEvent;
use App\User;
use App\Support\AppInstaller;
use App\Support\AppStore;

class AppInstallerTest extends TestCase
{
    use RefreshDatabase;

    public function testItInstall()
    {
        $installer = new AppInstaller;

        $email = 'admin@acme.org';

        $fakeNow = Carbon::create(2019, 5, 21);
        Carbon::setTestNow($fakeNow);

        $this->assertDatabaseMissing('roles', [
            'name' => Role::ADMIN,
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);

        $this->assertNull(AppStore::get(ServiceEvent::LATEST));

        $installer->install($email, 'secret123');

        $this->assertDatabaseHas('roles', [
            'name' => Role::ADMIN,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);

        $admin = User::where('email', $email)->firstOrFail();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->hasRole('unknown'));
        $this->assertNotNull($admin->email_verified_at);

        $latest = AppStore::get(ServiceEvent::LATEST);

        $this->assertInstanceOf(Carbon::class, $latest);
        $this->assertTrue($fakeNow->eq($latest));

        $nextYear = Carbon::create(2020, 5, 21);
        Carbon::setTestNow($nextYear);

        $installer->install($email, 'secret123');

        $this->assertTrue(
            $admin->roles()
                ->where('name', Role::ADMIN)
                ->count() === 1
        );

        $latest = AppStore::get(ServiceEvent::LATEST);

        $this->assertInstanceOf(Carbon::class, $latest);
        $this->assertTrue($fakeNow->eq($latest));
        $this->assertFalse($nextYear->eq($latest));

        Carbon::setTestNow(); // Clear mock
    }

    public function testItThrowExceptionIfBadUserPassword()
    {
        $installer = new AppInstaller;

        $this->expectException(\Exception::class);

        $installer->install('good@email.com', 'short');
    }

    public function testItThrowExceptionIfBadUserEmail()
    {
        $installer = new AppInstaller;

        $this->expectException(\Exception::class);

        $installer->install('bad.email', 'secret123');
    }
}
