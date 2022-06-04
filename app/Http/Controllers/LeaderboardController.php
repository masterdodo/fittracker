<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class LeaderboardController extends Controller
{
    public function index() {
        $users = User::all();
        $board = [];
        foreach($users as $user) {
            if ($user->type == "admin") {
                continue;
            }
            $count = 0;
            $count += $user->weight_activities->count();
            $count += $user->feeling_activities->count();
            $count += $user->routine_activities->count();
            $board[$user->email] =  $count;
        }
        arsort($board);
        return $board;
    }
}
