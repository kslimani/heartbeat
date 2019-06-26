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

        $fakeNow = Carbon::create(2019, 5, 21);
        Carbon::setTestNow($fakeNow);

        $this->assertDatabaseMissing('roles', [
            'name' => Role::ADMIN,
        ]);

        $this->assertNull(AppStore::get(ServiceEvent::LATEST));

        $installer->install();

        $this->assertDatabaseHas('roles', [
            'name' => Role::ADMIN,
        ]);

        $latest = AppStore::get(ServiceEvent::LATEST);

        $this->assertInstanceOf(Carbon::class, $latest);
        $this->assertTrue($fakeNow->eq($latest));

        $nextYear = Carbon::create(2020, 5, 21);
        Carbon::setTestNow($nextYear);

        $installer->install();

        $latest = AppStore::get(ServiceEvent::LATEST);

        $this->assertInstanceOf(Carbon::class, $latest);
        $this->assertTrue($fakeNow->eq($latest));
        $this->assertFalse($nextYear->eq($latest));

        Carbon::setTestNow(); // Clear mock
    }

    public function testItCreateAdminUser()
    {
        $installer = new AppInstaller;
        $installer->install();

        $name = 'Admin';
        $email = 'admin@acme.org';

        $this->assertDatabaseMissing('users', [
            'name' => $name,
            'email' => $email,
        ]);

        $admin = $installer->createAdminUser($name, $email, 'secret123');

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->hasRole('unknown'));
        $this->assertNotNull($admin->email_verified_at);

        $againWithSameEmail = $installer->createAdminUser('AnotherName', $email, 'secret123');

        $this->assertSame($admin->id, $againWithSameEmail->id);
        $this->assertTrue(
            $admin->roles()
                ->where('name', Role::ADMIN)
                ->count() === 1
        );
    }

    public function testItThrowExceptionIfBadUserPassword()
    {
        $installer = new AppInstaller;

        $this->expectException(\Exception::class);

        $installer->createAdminUser('Admin', 'good@email.com', 'short');
    }

    public function testItThrowExceptionIfBadUserEmail()
    {
        $installer = new AppInstaller;

        $this->expectException(\Exception::class);

        $installer->createAdminUser('Admin', 'bad.email', 'secret123');
    }
}
