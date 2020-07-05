<?php

namespace App\Support\Status;

use App\ServiceStatus;
use App\Status;
use App\Support\Utils;
use Illuminate\Support\Carbon;

class InactiveHandler
{
    /**
     * Handle inactive device service status
     *
     * @return void
     */
    public static function handle()
    {
        $inactive = Status::inactive()->id;
        $duration = config('app.status_inactive_duration');
        $debug = config('app.debug');

        ServiceStatus::chunk(200, function ($serviceStatuses) use ($inactive, $duration, $debug) {
            foreach ($serviceStatuses as $serviceStatus) {
                if (Utils::intNotEquals($serviceStatus->status_id, $inactive)) {
                    $elapsed = Utils::elapsed($serviceStatus->updated_at);

                    // Set service status to "inactive"
                    if ($elapsed >= $duration) {
                        $serviceStatus->status_id = $inactive;
                        $serviceStatus->updated_by = null;
                        $serviceStatus->changed_at = Carbon::now();
                        $serviceStatus->save();

                        // Status has changed
                        $event = StatusEvent::dispatch($serviceStatus);

                        // Log service event only if debug is enabled
                        $debug && Utils::logServiceEvent($event);
                    }
                }
            }
        });
    }
}
