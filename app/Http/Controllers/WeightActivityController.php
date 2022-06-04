<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WeightActivity;

class WeightActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        return $user->weight_activities;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $request->validate([
            'date' => ['required', 'before_or_equal:' . now()->format('Y-m-d')],
            'weight' => ['required', 'regex:/^(?:[1-9]\d+|\d)(?:\.\d)?$/'],
        ]);

        $weight_activities = $user->weight_activities;
        $weight_activity = $weight_activities->where("date", $request->date)->first();
        if ($weight_activity == null) {
            $weight_activity = new WeightActivity();
            $weight_activity->date = $request->date;
            $weight_activity->weight = $request->weight;
            $weight_activity->user_id = $user->id;
            $weight_activity->save();
            return response()->json(["message", "Activity added."], 200);
        }
        else {
            $weight_activity->weight = $request->weight;
            $weight_activity->save();
            return response()->json(["message", "Activity updated."], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $weight_activity = WeightActivity::find($id);
        if ($weight_activity == null) {
            return response()->json(["message" => "Invalid activity provided"], 400);
        }
        if ($weight_activity->user_id == $user->id) {
            $weight_activity->delete();
            return response()->json(["message" => "Activity deleted."], 200);
        }
        return response()->json(["message" => "User doesn't match. Access denied."], 400);
    }
}
