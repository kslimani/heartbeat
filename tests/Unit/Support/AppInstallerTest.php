<?php

namespace Tests\Unit;

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

        $email = 'admin@acme.org';

        $this->assertDatabaseMissing('roles', [
            'name' => Role::ADMIN,
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);

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
