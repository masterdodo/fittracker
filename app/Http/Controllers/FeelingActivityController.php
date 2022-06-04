<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FeelingActivity;

class FeelingActivityController extends Controller
{

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
            'feeling' => 'required|regex:/^feeling-[1-5]$/',
        ]);

        $feeling_activities = $user->feeling_activities;
        $feeling_activity = $feeling_activities->where("date", $request->date)->first();
        if ($feeling_activity == null) {
            $feeling_activity = new FeelingActivity();
            $feeling_activity->date = $request->date;
            $feeling_activity->feeling = $request->feeling;
            $feeling_activity->user_id = $user->id;
            $feeling_activity->save();
            return response()->json(["message", "Activity added."], 200);
        }
        else {
            $feeling_activity->feeling = $request->feeling;
            $feeling_activity->save();
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

        $feeling_activity = FeelingActivity::find($id);
        if ($feeling_activity == null) {
            return response()->json(["message" => "Invalid activity provided"], 400);
        }

        if ($feeling_activity->user_id == $user->id) {
            $feeling_activity->delete();
            return response()->json(["message" => "Activity deleted."], 200);
        }
        return response()->json(["message" => "User doesn't match. Access denied."], 400);
    }
}
