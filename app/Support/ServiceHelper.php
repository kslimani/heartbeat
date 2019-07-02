<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use IteratorAggregate;
use App\ServiceEvent;
use App\ServiceStatus;
use App\Support\Utils;
use App\User;

class ServiceHelper
{
    public static function serviceStatusIdList(User $user)
    {
        return DB::table('service_status_user')
            ->select('service_status_id')
            ->where('user_id', $user->id)
            ->pluck('service_status_id');
    }

    public static function eventsFromIdList(Collection $serviceStatusIdList)
    {
        return ServiceEvent::with([
                'fromStatus',
                'toStatus',
                'serviceStatus.device',
                'serviceStatus.service',
            ])
            ->whereIn('service_status_id', $serviceStatusIdList)
            ->orderBy('created_at', 'desc');
    }

    public static function eventsFromId($serviceStatusId)
    {
        return ServiceEvent::with([
                'fromStatus',
                'toStatus',
                'serviceStatus.device',
                'serviceStatus.service',
            ])
            ->where('service_status_id', $serviceStatusId)
            ->orderBy('created_at', 'desc');
    }

    public static function formatEvents(IteratorAggregate $events)
    {
        $events->each(function($event) {
            $event->label_date = Utils::humanDatetime($event->created_at);
            $event->label_duration = Utils::humanDuration(
                $event->elapsed ?: Utils::elapsed($event->created_at)
            );
        });
    }

    public static function statusesFromIdList(Collection $serviceStatusIdList)
    {
        return ServiceStatus::with([
                'device',
                'service',
                'status',
            ])
            ->whereIn('id', $serviceStatusIdList);
    }
}
