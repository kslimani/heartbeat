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
}
