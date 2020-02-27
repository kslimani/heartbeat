<?php

namespace App\Support\Status;

use App\ServiceStatus;
use App\Status;
use App\Support\Utils;

class StatusEvent
{
    /**
     * Dispatch service status event
     *
     * @param  \App\ServiceStatus  $serviceStatus
     * @return \App\ServiceEvent
     */
    public static function dispatch(ServiceStatus $serviceStatus)
    {
        // Attempt to retrieve latest service status event
        $latest = $serviceStatus->events()
            ->latest()
            ->first();

        if ($latest) {
            // Expects service status has changed
            $hasChanged = Utils::intNotEquals($latest->to_status_id, $serviceStatus->status_id);

            // Prevents event creation if status has not changed
            if (! $hasChanged) {
                return $latest;
            }

            // Set elapsed duration
            $latest->elapsed = Utils::elapsed($latest->created_at);
            $latest->save();
        }

        // Create new device service event
        $event = $serviceStatus->events()->create([
            'from_status_id' => $latest ? $latest->to_status_id : Status::inactive()->id,
            'to_status_id' => $serviceStatus->status_id,
            'is_handled' => false,
        ]);

        return $event;
    }
}
