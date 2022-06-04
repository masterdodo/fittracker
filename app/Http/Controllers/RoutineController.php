<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Routine;
use App\Models\Exercise;
use App\Models\User;

class RoutineController extends Controller
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

        return $user->routines;
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
            'routineName' => ['required', 'regex:/^[a-zA-Z0-9\s\p{Latin}]+$/'],
            'routineType' => ['required', 'regex:/^(Duration|Repetition)$/'],
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*' => ['regex:/^[a-zA-Z0-9\s\p{Latin}]+$/']
        ]);

        $routine = new Routine();
        $routine->name = $request->routineName;
        $routine->type = $request->routineType;
        $routine->user_id = $user->id;
        $routine->save();
        foreach($request->exercises as $ex) {
            $exercise = new Exercise();
            $exercise->name = $ex;
            $exercise->routine_id = $routine->id;
            $exercise->save();
        }
        return response()->json(['message' => 'Routine saved successfully.'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $routine = Routine::find($id);
        if ($routine == null) {
            return response()->json(["message" => "Invalid routine specified"], 400);
        }
        if ($routine->user_id != $user->id) {
            return response()->json(["message" => "You don't have access to edit the specified routine"], 400);
        }

        $routine->name = $request->routineName;
        $routine->type = $request->routineType;
        $routine->save();
        foreach($routine->exercises as $ex) {
            $x = 0;
            foreach($request->exercises as $key => $r_ex) {
                if ($r_ex["id"] == $ex->id) {
                    $x = 1;
                    $ex->name = $r_ex["name"];
                    $ex->save();
                    break;
                }
            }
            if ($x == 0) {
                foreach($ex->routine_activity_exercises as $rae) {
                    $rae->delete();
                }
                $ex->delete();
            }
        }
        foreach($request->exercises as $key => $r_ex) {
            $x = 0;
            foreach($routine->exercises as $ex) {
                if ($r_ex["id"] == $ex->id) {
                    $x = 1;
                    break;
                }
            }
            if ($x == 0) {
                $exercise = new Exercise();
                $exercise->name = $r_ex["name"];
                $exercise->routine_id = $routine->id;
                $exercise->save();
            }
        }
        return response()->json(['message' => 'Routine updated successfully.'], 200);
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

        $routine = Routine::find($id);
        if ($routine == null) {
            return response()->json(["message" => "Invalid routine specified"], 400);
        }
        if ($routine->user_id != $user->id) {
            return response()->json(["message" => "You don't have access to delete the specified routine"], 400);
        }

        foreach($routine->exercises as $ex) {
            foreach($ex->routine_activity_exercises as $rae) {
                $rae->delete();
            }
            $ex->delete();
        }
        foreach($routine->routine_activities as $ra) {
            $ra->delete();
        }
        $routine->delete();
        return response()->json(['message'=>'Routine deleted successfully.'], 200);
    }
}
