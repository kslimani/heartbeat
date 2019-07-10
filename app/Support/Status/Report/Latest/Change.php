<?php

namespace App\Support\Status\Report\Latest;

use App\ServiceStatus;
use App\Status;
use Illuminate\Support\Carbon;

class Change
{
    public $id;
    public $device;
    public $service;
    public $from;
    public $to;
    public $date;

    public function __construct(ServiceStatus $serviceStatus, Status $from, Status $to, Carbon $date)
    {
        $this->id = $serviceStatus->id;
        $this->device = $serviceStatus->device;
        $this->service = $serviceStatus->service;
        $this->from = $from;
        $this->to = $to;
        $this->date = $date;
    }
}
