<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use IteratorAggregate;
use App\ServiceEvent;
use App\ServiceStatus;
use App\Support\Locale;
use App\Support\Utils;
use App\User;

class ServiceHelper
{
    const DEFAULT_WITH = ['device', 'service', 'status'];

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
        $events->each(function ($event) {
            $event->label_date = Locale::humanDatetime($event->created_at);
            $event->label_duration = Locale::humanDuration(
                $event->elapsed ?: Utils::elapsed($event->created_at)
            );
        });
    }

    public static function statusesFromIdList(Collection $serviceStatusIdList)
    {
        return ServiceStatus::with(self::DEFAULT_WITH)
            ->whereIn('id', $serviceStatusIdList);
    }

    public static function sortableStatuses($query)
    {
        // FIXME: find a better way to sort by relations ?
        return $query->leftJoin('devices', 'service_statuses.device_id', '=', 'devices.id')
            ->leftJoin('services', 'service_statuses.service_id', '=', 'services.id')
            ->select('service_statuses.*')
            ->addSelect('devices.id as device_id')
            ->addSelect('services.id as service_id');
    }

    public static function userStatuses(User $user, array $with = self::DEFAULT_WITH)
    {
        return self::sortableStatuses($user->serviceStatuses()->with($with));
    }

    public static function statuses(array $with = self::DEFAULT_WITH)
    {
        return self::sortableStatuses(ServiceStatus::with($with));
    }
}
