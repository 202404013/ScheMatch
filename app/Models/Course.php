<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['user_id', 'school_year', 'semester', 'class_code', 'subject', 'section', 'professor'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}