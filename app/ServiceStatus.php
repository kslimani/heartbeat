<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceStatus extends Model
{
    protected $table = 'service_status';

    protected $fillable = [
        'service_id',
        'status_id',
        'updated_by', // user id or NULL
        'updated_at',
    ];

    protected $dates = [
        'updated_at',
    ];

    public $timestamps = false;

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

    public function events()
    {
        return $this->hasMany('App\ServiceEvent');
    }
}
