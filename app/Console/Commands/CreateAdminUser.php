<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Support\AppInstaller;

class CreateAdminUser extends Command
{
    const DEFAULT_ADMIN_NAME = "Admin";
    const DEFAULT_ADMIN_EMAIL = "admin@localhost";
    const DEFAULT_ADMIN_PASSWORD = "password";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user
        {--name= : The name of admin user}
        {--email= : The email address of admin user}
        {--password= : The clear text password of admin user}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user with "admin" role';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(AppInstaller $installer)
    {
        if (! $name = $this->option('name')) {
            $name = self::DEFAULT_ADMIN_NAME;
        }

        if (! $email = $this->option('email')) {
            $email = self::DEFAULT_ADMIN_EMAIL;
        }

        if (! $password = $this->option('password')) {
            $password = self::DEFAULT_ADMIN_PASSWORD;
        }

        $this->comment(' Create new "admin" user with following informations :');
        $this->line('');
        $this->info(sprintf(' NAME     : %s', $name));
        $this->info(sprintf(' EMAIL    : %s', $email));
        $this->info(sprintf(' PASSWORD : %s', $password));

        if ($this->confirm('Do you wish to continue ?')) {
            try {
                DB::transaction(function () use ($installer, $name, $email, $password) {
                    $installer->createAdminUser($name, $email, $password);
                });
            } catch (\Exception $e) {
                return $this->error(sprintf(
                    'Failed to create user : %s',
                    $e->getMessage()
                ));
            }

            $this->info('User successfully created!');
        }
    }
}
