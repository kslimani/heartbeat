<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceStatus extends Model
{
    protected $table = 'service_statuses';

    protected $fillable = [
        'service_id',
        'status_id',
        'updated_by', // User id or NULL if scheduled task
        'changed_at',
    ];

    protected $dates = [
        'changed_at',
    ];

    public function device()
    {
        return $this->belongsTo('App\Device', 'device_id');
    }

    public function service()
    {
        return $this->belongsTo('App\Service', 'service_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Status', 'status_id');
    }

    public function events()
    {
        return $this->hasMany('App\ServiceEvent');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
