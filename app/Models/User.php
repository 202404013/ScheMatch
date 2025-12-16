<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = ['name', 'email', 'password'];

    public function availabilities() {
        return $this->hasMany(Availability::class);
    }

    public function friends() {
        return $this->hasMany(Friend::class, 'user_id');
    }

    public function friendOf() {
        return $this->hasMany(Friend::class, 'friend_user_id');
    }
}
