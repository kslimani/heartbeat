<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceEvent extends Model
{
    const LATEST = '_latest_service_event';

    protected $table = 'service_events';

    protected $fillable = [
        'service_status_id',
        'from_status_id',
        'to_status_id',
        'elapsed',    // Duration in seconds, NULL by default
    ];

    public function serviceStatus()
    {
        return $this->belongsTo('App\serviceStatus', 'service_status_id');
    }

    public function fromStatus()
    {
        return $this->belongsTo('App\Status', 'from_status_id');
    }

    public function toStatus()
    {
        return $this->belongsTo('App\Status', 'to_status_id');
    }
}
