<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceEvent extends Model
{
    const LATEST = 'latest_service_event';

    protected $table = 'service_events';

    protected $fillable = [
        'service_status_id',
        'status_id',
        'elapsed',    // Duration in seconds, NULL by default
        'updated_by', // user id or NULL
    ];

    public function serviceStatus()
    {
        return $this->belongsTo('App\serviceStatus', 'service_status_id');
    }

    public function status()
    {
        return $this->hasOne('App\Status', 'status_id');
    }
}
