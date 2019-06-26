<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Support\AppInstaller;

class InstallApplication extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

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
        try {
            DB::transaction(function () use ($installer) {
                $installer->install();
            });
        } catch (\Exception $e) {
            return $this->error(sprintf(
                'Installation failed: %s',
                $e->getMessage()
            ));
        }

        $this->info('Installation complete!');
    }
}
