<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Availability extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 
        'date', 
        'start_time', 
        'end_time', 
        'visibility'
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',  
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function sharedWith() {
        return $this->hasMany(SharedWith::class);
    }
}