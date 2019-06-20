<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Support\AppInstaller;
use App\Support\LogSql;
use App\Role;
use App\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $adminEmail = 'admin@test.com';
    public $adminSecret = 'secret123';

    public function installApplication($email, $password)
    {
        (new AppInstaller)->install($email, $password);
    }

    public function createAdminUser(array $attributes = [])
    {
        $user = factory(User::class)->create($attributes);

        Role::admin()->users()->attach([$user->id]);

        return $user;
    }

    public function setupApp()
    {
        $this->installApplication(
            $this->adminEmail,
            $this->adminSecret
        );
    }

    public function debugSql()
    {
        LogSql::debug();
    }
}
