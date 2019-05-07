<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';

    protected $fillable = [
        'name',
    ];

    public function serviceStatuses()
    {
        return $this->hasMany('App\ServiceStatus');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
