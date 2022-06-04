<?php

namespace App\Http\Controllers;

use DateTime;

use Illuminate\Http\Request;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function profile() {
        return view('profile');
    }

    public function profile_weight(Request $request) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $was = $user->weight_activities;
        $out = [];
        foreach($was as $wa) {
            $out[$wa->date] = $wa->weight;
        }
        ksort($out);
        return $out;
    }

    public function profile_activities(Request $request) {
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
        ksort($out);
        
        $begin = new DateTime(array_key_first($out));
        $end = new DateTime(array_key_last($out));

        for($i = $begin; $i <= $end; $i->modify('+1 day')){
            if (!array_key_exists($i->format("Y-m-d"), $out)) {
                $out[$i->format("Y-m-d")] = 0;
            }
        }
        ksort($out);
        return $out;
    }
}
