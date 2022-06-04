<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    public function routine()
    {
        return $this->belongsTo('App\Models\Routine');
    }

    public function routine_activity_exercises()
    {
        return $this->hasMany('App\Models\RoutineActivityExercise');
    }
}
