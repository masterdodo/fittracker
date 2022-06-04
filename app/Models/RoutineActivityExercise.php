<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutineActivityExercise extends Model
{
    use HasFactory;

    public function routine_activity()
    {
        return $this->belongsTo('App\Models\RoutineActivity');
    }

    public function exercise()
    {
        return $this->belongsTo('App\Models\Exercise');
    }
}
