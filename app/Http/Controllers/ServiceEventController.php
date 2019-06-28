<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\ServiceEvent;
use App\Support\Utils;

class ServiceEventController extends Controller
{
    public function index(Request $request)
    {
        // Get user service statuses id list
        $userServicesStatuses = DB::table('service_status_user')
            ->select('service_status_id')
            ->where('user_id', $request->user()->id)
            ->pluck('service_status_id');

        // Get user service events
        $events = ServiceEvent::whereIn('service_status_id', $userServicesStatuses)
            ->with([
                'fromStatus',
                'toStatus',
                'serviceStatus.device',
                'serviceStatus.service',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(config('app.pagination_limit'));

        $events->each(function($event) {
            $event->label_date = Utils::humanDatetime($event->created_at);
            $event->label_duration = Utils::humanDuration(
                $event->elapsed ?: Utils::elapsed($event->created_at)
            );
        });

        return view('service-events/index', [
            'events' => $events,
        ]);
    }
}
