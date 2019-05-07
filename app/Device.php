<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';

    protected $fillable = [
        'name',
    ];

    public function servicesStatus()
    {
        return $this->belongsToMany('App\ServiceStatus', 'device_service_status', 'device_id', 'service_status_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function events()
    {
        return $this->hasMany('App\DeviceEvent');
    }
}
