<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutineActivity extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function routine()
    {
        return $this->belongsTo('App\Models\Routine');
    }

    public function routine_activity_exercises()
    {
        return $this->hasMany('App\Models\RoutineActivityExercise');
    }
}
