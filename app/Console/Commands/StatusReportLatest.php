<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Support\Status\InactiveHandler;
use App\Support\Status\Report\Latest\Reporter;

class StatusReportLatest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hb:report-status-change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[SCHEDULED] Report services status changes to users';

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
    public function handle()
    {
        // Check for inactive services
        DB::transaction(function () {
            InactiveHandler::handle();
        });

        // Report any services status changes to users
        DB::transaction(function () {
            Reporter::report();
        });
    }
}
