<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Routine;

class ExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }

        $routine = Routine::find($id);
        if ($routine == null) {
            return response()->json(["message" => "Invalid routine provided"], 400);
        }
        if ($user->id != $routine->user_id) {
            return response()->json(["message" => "User can't access provided routine"], 400);
        }

        return $routine->exercises;
    }
}
