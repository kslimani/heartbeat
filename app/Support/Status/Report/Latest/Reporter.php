<?php

namespace App\Support\Status\Report\Latest;

use App\Notifications\StatusHasChanged;
use App\ServiceEvent;
use App\ServiceStatus;
use App\Status;
use App\Support\AppStore;
use App\Support\Utils;
use App\User;
use Illuminate\Support\Carbon;

class Reporter
{
    public static function report()
    {
        // Check if all devices are muted
        if (Utils::allDeviceMuted()) {
            return;
        }

        $now = Carbon::now();
        $latest = AppStore::get(ServiceEvent::LATEST);

        // Murphy's law
        if (! $latest) {
            throw new \LogicException('Failed to get latest service event date');
        }

        // Make report and notify to users
        self::notify(self::make($latest));

        // Update latest date
        AppStore::put(ServiceEvent::LATEST, $now);
    }

    public static function make(Carbon $latest)
    {
        $report = new Report;

        // Get devices services events since latest date
        ServiceEvent::with([
                'fromStatus',
                'toStatus',
                'serviceStatus.device',
                'serviceStatus.service',
            ])
            ->where('created_at', '>', $latest)
            ->orderby('created_at', 'asc')
            ->get()
            ->groupBy('service_status_id')
            ->each(function($serviceStatusHistory) use ($report) {
                // Get first and last event from history
                $first = $serviceStatusHistory->first();
                $last = $serviceStatusHistory->last();

                // Check if service status is different from latest date
                if ($first->from_status_id !== $last->to_status_id) {
                    $report->statusHasChanged(
                        $first->serviceStatus,
                        $first->fromStatus,
                        $last->toStatus,
                        $last->created_at
                    );
                }
            });

        return $report;
    }

    public static function notify(Report $report)
    {
        // Ensure report has changes
        if ($report->changes()->isEmpty()) {

            return;
        }

        // Notify changes to users
        User::with(['settings'])
            ->whereHas('serviceStatuses', function ($query) use ($report) {
                $query->where('is_mute', false)->whereIn('id', $report->changesById()->keys());
            })
            ->chunk(50, function($users) use ($report) {
                foreach ($users as $user) {
                    $user->notify(new StatusHasChanged($report));
                }
            });
    }
}
