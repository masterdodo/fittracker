<?php

namespace App\Http\Controllers;

use DateTime;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Routine;
use App\Models\RoutineActivity;
use App\Models\RoutineActivityExercise;

class RoutineActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function streak(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $today = new DateTime(date("Y-m-d"));
        $streak = 0;
        while (true) {
            foreach($user->routine_activities as $ra) {
                if ($ra->date == $today->format("Y-m-d")) {
                    $streak++;
                    $today = $today->modify('-1 day');
                    continue 2;
                }
            }
            break;
        }
        return $streak;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        error_log($request);
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $request->validate([
            'date' => ['required', 'before_or_equal:' . now()->format('Y-m-d')],
            'routine' => 'required',
            'routine.sets' => 'required|integer',
            'routine.type' => ['required', 'regex:/^(Duration|Repetitions)$/'],
            'routine.exercises' => ['required', 'array', 'min:1'],
            'routine.exercises.*.id' => ['required', 'numeric'],
            'routine.exercises.*.duration' => ['required_if:routine.type,==,Duration', 'integer'],
            'routine.exercises.*.repetitions' => ['required_if:routine.type,==,Repetition', 'integer'],
            'routine.exercises.*.timeUnit' => ['required_if:routine.exercises.*.duration,!=,null', 'regex:/^(sec|min)$/'],
        ]);
        
        $date = $request->date;
        $routine = Routine::find($request->routine["id"]);
        
        if ($routine == null) {
            return response()->json(["message" => "Routine doesn't exist"], 400);
        }

        $routine_activity = new RoutineActivity();
        $routine_activity->date = $request->date;
        $routine_activity->sets = $request->routine["sets"];
        $routine_activity->user_id = $user->id;
        $routine_activity->routine_id = $routine->id;
        $routine_activity->save();
        foreach($routine->exercises as $ex) {
            foreach($request->routine["exercises"] as $r_ex) {
                if ($ex->id == $r_ex["id"]) {
                    $ra_ex = new RoutineActivityExercise();
                    $ra_ex->exercise_id = $ex->id;
                    $ra_ex->routine_activity_id = $routine_activity->id;
                    if ($request->routine["type"] == "Duration") {
                        $ra_ex->amount = $r_ex["duration"];
                        $ra_ex->unit = $r_ex["timeUnit"];
                    }
                    else if ($request->routine["type"] == "Repetitions") {
                        $ra_ex->amount = $r_ex["repetitions"];
                    }
                    else {
                        return response()->json(["message" => "Problem with the exercise."], 400);
                    }
                    $ra_ex->save();
                }
            }
        }
        return response()->json(["message" => "Activity added."], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $routine_activity = RoutineActivity::find($id);
        if ($routine_activity == null) {
            return response()->json(["message" => "Activity not found."], 400);
        }

        $out = $routine_activity;

        $routine = Routine::find($routine_activity->routine_id);
        if ($routine == null) {
            return response()->json(["message" => "Routine in activity not found."], 400);
        }

        $out->routine_id = $routine;

        $exercies = [];
        foreach($routine_activity->routine_activity_exercises as $rae) {
            $exercise = $rae->exercise;
            $exercise->amount = $rae->amount;
            $exercise->unit = $rae->unit;
            array_push($exercies, $exercise);
        }
        $out->exercises = $exercies;

        return $out;
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

        $routine_activity = RoutineActivity::find($id);
        if ($routine_activity == null) {
            return response()->json(["message" => "Invalid routine activity provided"], 400);
        }
        if ($routine_activity->user_id == $user->id) {
            foreach($routine_activity->routine_activity_exercises as $rae) {
                $rae->delete();
            }
            $routine_activity->delete();
            return response()->json(["message" => "Activity deleted."], 200);
        }
        return response()->json(["message" => "User doesn't match. Access denied."], 400);
    }
}
