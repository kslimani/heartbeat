<?php

namespace App\Support\Status\Report\Latest;

use App\ServiceStatus;
use App\Status;
use App\User;
use Illuminate\Support\Carbon;

class Report
{
    protected $changes;

    public function __construct()
    {
        $this->changes = collect();
    }

    public function statusHasChanged(ServiceStatus $serviceStatus, Status $from, Status $to, Carbon $date)
    {
        $this->changes->push(new Change($serviceStatus, $from, $to, $date));
    }

    public function changes()
    {
        return $this->changes;
    }

    public function changesById()
    {
        return $this->changes->groupBy('id');
    }

    public function userChanges(User $user)
    {
        // Get user's unmuted services ids
        $ids = $user->serviceStatuses()
            ->select('id')
            ->where('is_mute', false)
            ->pluck('id');

        // Filters changes
        return $this->changes()->filter(function($change) use ($ids) {
            return $ids->contains($change->id);
        });
    }

    public function userStatuses(User $user)
    {
        $statuses = [
            'INACTIVE' => 0,
            'DOWN' => 0,
            'UP' => 0,
        ];

        // FIXME: find a better way to retrieve counts
        $user->serviceStatuses()
            ->with('status')
            ->each(function ($item) use (&$statuses) {
                switch ($item->status->name) {
                    case 'INACTIVE':
                        ++$statuses['INACTIVE'];
                        break;

                    case 'DOWN':
                        ++$statuses['DOWN'];
                        break;

                    case 'UP':
                        ++$statuses['UP'];
                        break;
                }
            });

        return $statuses;
    }
}
