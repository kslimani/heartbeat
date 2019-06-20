<?php

namespace App\Support\Status;

use App\ServiceStatus;
use App\Status;
use App\Support\Utils;

class InactiveHandler
{
    /**
     * Handle inactive device service status
     *
     * @throws \App\Status\StatusException
     */
    public static function handle()
    {
        $inactive = Status::inactive()->id;
        $duration = config('app.status_inactive_duration');

        ServiceStatus::get()
            ->each(function($serviceStatus) use ($inactive, $duration) {
                if ($serviceStatus->status_id !== $inactive) {
                    $elapsed = Utils::elapsed($serviceStatus->updated_at);

                    // Set service status to "inactive"
                    if ($elapsed >= $duration) {
                        $serviceStatus->status_id = $inactive;
                        $serviceStatus->updated_by = null;
                        $serviceStatus->save();

                        // Status has changed
                        StatusEvent::dispatch($serviceStatus);
                    }
                }
            });
    }
}
