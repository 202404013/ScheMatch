<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SharedWith extends Model
{
    use SoftDeletes;

    protected $fillable = ['availability_id', 'friend_id'];

    public function availability() {
        return $this->belongsTo(Availability::class);
    }

    public function friend() {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
