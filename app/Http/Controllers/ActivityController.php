<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WeightActivity;
use App\Models\FeelingActivity;
use App\Models\RoutineActivity;

class ActivityController extends Controller
{
    public function index(Request $request) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $wa = $user->weight_activities->toArray();
        $fa = $user->feeling_activities->toArray();
        $ra = $user->routine_activities->toArray();
        return array_merge($wa, $fa, $ra);
    }

    public function index_date(Request $request) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $wa = $user->weight_activities->where("date", $request->date)->toArray();
        $fa = $user->feeling_activities->where("date", $request->date)->toArray();
        $ra = $user->routine_activities->where("date", $request->date)->toArray();
        return array_merge($wa, $fa, $ra);
    }

    public function index_dates(Request $request) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $out = [];
        $wa = $user->weight_activities;
        foreach($wa as $wa1) {
            if (!array_key_exists($wa1->date, $out)) {
                $out[$wa1->date] = 1;
            }
            else {
                $out[$wa1->date]++;
            }
        }
        $fa = $user->feeling_activities;
        foreach($fa as $fa1) {
            if (!array_key_exists($fa1->date, $out)) {
                $out[$fa1->date] = 1;
            }
            else {
                $out[$fa1->date]++;
            }
        }
        $ra = $user->routine_activities;
        foreach($ra as $ra1) {
            if (!array_key_exists($ra1->date, $out)) {
                $out[$ra1->date] = 1;
            }
            else {
                $out[$ra1->date]++;
            }
        }
        
        krsort($out);
        return $out;
    }

    function like_match($pattern, $subject) {
        $pattern = str_replace('%', '.*', preg_quote($pattern, '/'));
        return (bool) preg_match("/^{$pattern}$/i", $subject);
    }

    public function search_r_dates(Request $request, $query) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $out = [];
        $ra = $user->routine_activities;
        foreach($ra as $ra1) {
            if(!$this->like_match("%$query%", $ra1->routine->name)) {
                continue;
            }
            if (!array_key_exists($ra1->date, $out)) {
                $out[$ra1->date] = 1;
            }
            else {
                $out[$ra1->date]++;
            }
        }
        
        krsort($out);
        return $out;
    }

    public function search_d_dates(Request $request, $query) {
        $months = ["01" => "Januar", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December"];

        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $out = [];
        $wa = $user->weight_activities;
        foreach($wa as $wa1) {
            $d1 = $wa1->date;
            $d2 = $months[explode("-", $wa1->date)[1]];
            
            if(!($this->like_match("%$query%", $d1) || $this->like_match("%$query%", $d2))) {
                continue;
            }
            if (!array_key_exists($wa1->date, $out)) {
                $out[$wa1->date] = 1;
            }
            else {
                $out[$wa1->date]++;
            }
        }
        $fa = $user->feeling_activities;
        foreach($fa as $fa1) {
            $d1 = $fa1->date;
            $d2 = $months[explode("-", $fa1->date)[1]];

            if(!($this->like_match("%$query%", $d1) || $this->like_match("%$query%", $d2))) {
                continue;
            }
            if (!array_key_exists($fa1->date, $out)) {
                $out[$fa1->date] = 1;
            }
            else {
                $out[$fa1->date]++;
            }
        }
        $ra = $user->routine_activities;
        foreach($ra as $ra1) {
            $d1 = $ra1->date;
            $d2 = $months[explode("-", $ra1->date)[1]];
            
            if(!($this->like_match("%$query%", $d1) || $this->like_match("%$query%", $d2))) {
                continue;
            }
            if (!array_key_exists($ra1->date, $out)) {
                $out[$ra1->date] = 1;
            }
            else {
                $out[$ra1->date]++;
            }
        }
        
        krsort($out);
        return $out;
    }

    public function search_r(Request $request, $query) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $out = [];
        $ra = $user->routine_activities;
        foreach($ra as $ra1) {
            if(!$this->like_match("%$query%", $ra1->routine->name)) {
                continue;
            }
            $out[] = $ra1;
        }
        return $out;
    }

    public function search_r_date(Request $request, $query) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $out = [];
        $wa = $user->weight_activities->where("date", $request->date);
        foreach($wa as $wa1) {
            $out[] = $wa1;
        }
        $fa = $user->feeling_activities->where("date", $request->date);
        foreach($fa as $fa1) {
            $out[] = $fa1;
        }
        $ra = $user->routine_activities->where("date", $request->date);
        foreach($ra as $ra1) {
            if(!$this->like_match("%$query%", $ra1->routine->name)) {
                continue;
            }
            $out[] = $ra1;
        }
        return $out;
    }

}
