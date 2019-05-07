<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceEvent extends Model
{
    protected $table = 'device_events';

    protected $fillable = [
        'device_id',
        'service_id',
        'status_id',
        'elapsed',    // Duration in seconds, NULL by default
        'updated_by', // user id or NULL
    ];

    public function device()
    {
        return $this->belongsTo('App\Device', 'device_id');
    }

    public function service()
    {
        return $this->hasOne('App\Service', 'service_id');
    }

    public function status()
    {
        return $this->hasOne('App\Status', 'status_id');
    }
}
