<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function index(Request $request) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }
        if ($user->type != "admin") {
            return response->json(["message" => "Only administrators have access to this feature"], 400);
        }

        $users = User::where("type", "user")->get();
        return $users;
    }

    public function delete_routines(Request $request, $user_id) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }
        if ($user->type != "admin") {
            return response->json(["message" => "Only administrators have access to this feature"], 400);
        }

        $u = User::find($user_id);
        if ($u == null) {
            return response()->json(["message" => "User with given id doesn't exist"], 400);
        }

        foreach($u->routines as $r) {
            foreach($r->routine_activities as $ra) {
                foreach($ra->routine_activity_exercises as $rae) {
                    $rae->delete();
                }
                $ra->delete();
            }
            foreach($r->exercises as $ex) {
                $ex->delete();
            }
            $r->delete();
        }
        return response()->json(["message" => "Routines deleted successfully"], 200);
    }

    public function delete_activities(Request $request, $user_id) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }
        if ($user->type != "admin") {
            return response->json(["message" => "Only administrators have access to this feature"], 400);
        }

        $u = User::find($user_id);
        if ($u == null) {
            return response()->json(["message" => "User with given id doesn't exist"], 400);
        }

        foreach($u->routine_activities as $ra) {
            foreach($ra->routine_activity_exercises as $rae) {
                $rae->delete();
            }
            $ra->delete();
        }
        foreach($u->feeling_activities as $fa) {
            $fa->delete();
        }
        foreach($u->weight_activities as $wa) {
            $wa->delete();
        }
        return response()->json(["message" => "Activities deleted successfully"], 200);
    }

    public function delete_user(Request $request, $id) {
        $token = $request->bearerToken();
        $user = User::where("api_token", $token)->first();
        if ($user == null) {
            return response()->json(["message" => "Invalid api key provided"], 400);
        }
        if ($user->type != "admin") {
            return response->json(["message" => "Only administrators have access to this feature"], 400);
        }

        $u = User::find($id);
        if ($u == null) {
            return response()->json(["message" => "User with given id doesn't exist"], 400);
        }

        foreach($u->routine_activities as $ra) {
            foreach($ra->routine_activity_exercises as $rae) {
                $rae->delete();
            }
            $ra->delete();
        }
        foreach($u->feeling_activities as $fa) {
            $fa->delete();
        }
        foreach($u->weight_activities as $wa) {
            $wa->delete();
        }
        foreach($u->routines as $r) {
            foreach($r->exercises as $ex) {
                $ex->delete();
            }
            $r->delete();
        }
        $u->delete();
        return response()->json(["message" => "User deleted successfully"], 200);
    }
}
