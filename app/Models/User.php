<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Http\Models\Routine;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function routines()
    {
        return $this->hasMany('App\Models\Routine');
    }

    public function weight_activities()
    {
        return $this->hasMany('App\Models\WeightActivity');
    }

    public function feeling_activities()
    {
        return $this->hasMany('App\Models\FeelingActivity');
    }

    public function routine_activities()
    {
        return $this->hasMany('App\Models\RoutineActivity');
    }
}
