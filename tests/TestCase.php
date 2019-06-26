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

    public $adminName = 'Admin';
    public $adminEmail = 'admin@test.com';
    public $adminSecret = 'secret123';

    public function setupApp()
    {
        (new AppInstaller)->install()
            ->createAdminUser(
                $this->adminName,
                $this->adminEmail,
                $this->adminSecret
            );
    }

    public function debugSql()
    {
        LogSql::debug();
    }
}
