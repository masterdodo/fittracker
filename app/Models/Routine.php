<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routine extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function exercises()
    {
        return $this->hasMany('App\Models\Exercise');
    }

    public function routine_activities()
    {
        return $this->hasMany('App\Models\RoutineActivity');
    }
}
