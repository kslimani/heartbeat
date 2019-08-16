<?php

namespace Tests\Unit\Support;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Role;
use App\User;
use App\Support\AppInstaller;

class AppInstallerTest extends TestCase
{
    use RefreshDatabase;

    public function testItInstall()
    {
        $installer = new AppInstaller;

        $this->assertDatabaseMissing('roles', [
            'name' => Role::ADMIN,
        ]);

        $installer->install();

        foreach ([
            Role::ADMIN,
            Role::OVERSEER,
        ] as $role) {
            $this->assertDatabaseHas('roles', [
                'name' => $role,
            ]);
        }
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
