<?php

namespace App\Support\Status\Report\Latest;

use App\Notifications\StatusHasChanged;
use App\ServiceEvent;
use App\ServiceStatus;
use App\Status;
use App\Support\Utils;
use App\User;
use Illuminate\Support\Facades\Log;

class Reporter
{
    protected static $handled;

    public static function report()
    {
        self::$handled = collect();
        $notified = 0;

        // Make report
        $report = self::make();

        // Notify users (unless all devices are muted)
        if (! Utils::allDeviceMuted()) {
            $notified = self::notify($report);
        }

        // Log notified users count only if debug is enabled
        config('app.debug') && Log::debug(sprintf('[Reporter] %s user(s) notified', $notified));

        // Set all marked events has handled
        self::setEventsAsHandled();

        return $notified;
    }

    public static function make()
    {
        $report = new Report;

        // Get default report tolerance delay in seconds
        $defaultRtd = config('app.report_tolerance_delay');

        // Get unhandled devices services events
        ServiceEvent::with([
                'fromStatus',
                'toStatus',
                'serviceStatus.device',
                'serviceStatus.service',
            ])
            ->where('is_handled', '=', false)
            ->orderby('created_at', 'asc')
            ->get()
            ->groupBy('service_status_id')
            ->each(function ($serviceStatusHistory) use ($report, $defaultRtd) {
                // Get first and last event from history
                $first = $serviceStatusHistory->first();
                $last = $serviceStatusHistory->last();

                // Check for custom tolerance delay
                $toleranceDelay = $first->serviceStatus->rtd
                    ? $first->serviceStatus->rtd
                    : $defaultRtd;

                // Get all event id
                $eventIds = $serviceStatusHistory->pluck('id');

                // Check if service status has not changed
                if (Utils::intEquals($first->from_status_id, $last->to_status_id)) {
                    // Mark all events has "handled"
                    self::markAsHandled($eventIds);

                    // No changes to report
                    return;
                }

                // Get elapsed duration of last event
                $elapsed = $last->elapsed ? $last->elapsed : Utils::elapsed($last->created_at);

                // Check if elapsed duration is under tolerance delay
                if ($elapsed < $toleranceDelay) {
                    // Mark all events has "handled" except first and last event
                    $eventIds->shift();
                    $eventIds->pop();
                    self::markAsHandled($eventIds);

                    // Do not report changes yet
                    return;
                }

                // Mark all events has "handled"
                self::markAsHandled($eventIds);

                // Report status change
                $report->statusHasChanged(
                    $first->serviceStatus,
                    $first->fromStatus,
                    $last->toStatus,
                    $last->created_at
                );
            });

        return $report;
    }

    public static function markAsHandled($eventIds)
    {
        self::$handled = self::$handled->concat($eventIds);
    }

    public static function setEventsAsHandled()
    {
        // Set all events as handled using single db query
        if (self::$handled->isNotEmpty()) {
            ServiceEvent::whereIn('id', self::$handled)
                ->update(['is_handled' => true]);
        }
    }

    public static function notify(Report $report)
    {
        $notified = 0;

        // Ensure report has changes
        if ($report->changes()->isEmpty()) {
            return $notified;
        }

        // Notify changes to users
        User::with(['settings'])
            ->whereHas('serviceStatuses', function ($query) use ($report) {
                $query->where('is_mute', false)->whereIn('id', $report->changesById()->keys());
            })
            ->chunk(50, function ($users) use ($report, &$notified) {
                foreach ($users as $user) {
                    $user->notify(new StatusHasChanged($report));
                    ++$notified;
                }
            });

        return $notified;
    }
}
