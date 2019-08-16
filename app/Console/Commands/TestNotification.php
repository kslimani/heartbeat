<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\Test;
use App\User;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hb:test-notification {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a "test" notification to a user';

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
        $id = $this->argument('user_id');

        try {
            $user = User::findOrFail($id);
        } catch (\Exception $e) {
            return $this->error('User not found');
        }

        $this->info(sprintf(' User name  : %s', $user->name));
        $this->info(sprintf(' User email : %s', $user->email));

        if ($this->confirm('Do you wish to continue ?')) {
            try {
                $user->notify(new Test);
            } catch (\Exception $e) {
                return $this->error(sprintf(
                    'Failed to send notification : %s',
                    $e->getMessage()
                ));
            }

            $this->info('The notification has been sent');
        }
    }
}
