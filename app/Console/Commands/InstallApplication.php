<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Support\AppInstaller;

class InstallApplication extends Command
{
    const DEFAULT_ADMIN_EMAIL = "admin@localhost";
    const DEFAULT_ADMIN_PASSWORD = "password";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install 
        {--email= : The email address of admin user}
        {--password= : The clear text password of admin user}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install application';

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
        if (! $email = $this->option('email')) {
            $email = self::DEFAULT_ADMIN_EMAIL;
        }

        if (! $password = $this->option('password')) {
            $password = self::DEFAULT_ADMIN_PASSWORD;
        }

        $this->comment(' Install application with the following Admin user :');
        $this->line('');
        $this->info(sprintf(' EMAIL    : %s', $email));
        $this->info(sprintf(' PASSWORD : %s', $password));

        if ($this->confirm('Do you wish to continue ?')) {
            try {
                $installer->install($email, $password);
            } catch (\Exception $e) {
                return $this->error(sprintf(
                    'Installation failed: %s',
                    $e->getMessage()
                ));
            }

            $this->info('Installation complete!');
        }
    }
}
