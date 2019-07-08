<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_user');
    }

    public function hasRole($name)
    {
        // Roles loaded once in PHP process lifecycle
        return $this->roles->first(function($role) use ($name) {
            return $role->name === $name;
        }) !== null;
    }

    public function isAdmin()
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function authorizedKeys()
    {
        return $this->hasMany('App\AuthorizedKey');
    }

    public function serviceStatuses()
    {
        return $this->belongsToMany(
            'App\ServiceStatus',
            'service_status_user',
            'user_id',
            'service_status_id'
        )->withPivot('is_updatable', 'is_mute');
    }

    public function settings()
    {
        return $this->hasOne('App\UserSettings');
    }
}
