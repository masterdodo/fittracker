<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\WeightActivityController;
use App\Http\Controllers\FeelingActivityController;
use App\Http\Controllers\RoutineActivityController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\AdminController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware("auth:api")->get("/users/all", [AdminController::class, "index"]);

Route::prefix("/admin")->group( function() {
    Route::middleware("auth:api")->get("/routines/delete/{id}", [AdminController::class, "delete_routines"]);
    Route::middleware("auth:api")->get("/activities/delete/{id}", [AdminController::class, "delete_activities"]);
    Route::middleware("auth:api")->get("/users/delete/{id}", [AdminController::class, "delete_user"]);
});

Route::middleware("auth:api")->get("/profile/chart/weight", [HomeController::class, "profile_weight"]);
Route::middleware("auth:api")->get("/profile/chart/activities", [HomeController::class, "profile_activities"]);
Route::get("/leaderboard", [LeaderboardController::class, "index"]);

//Routines
Route::prefix("/routine")->group( function() {
    Route::middleware("auth:api")->get("/all", [RoutineController::class, "index"]);
    Route::middleware("auth:api")->post("/add", [RoutineController::class, "store"]);
    Route::middleware("auth:api")->post("/edit/{id}", [RoutineController::class, "update"]);
    Route::middleware("auth:api")->get("/delete/{id}", [RoutineController::class, "destroy"]);
});

//Activities
Route::prefix("/activity")->group( function() {
    Route::middleware("auth:api")->get("/streak", [RoutineActivityController::class, "streak"]);
    Route::middleware("auth:api")->get("/all", [ActivityController::class, "index"]);
    Route::middleware("auth:api")->post("/all/date", [ActivityController::class, "index_date"]);
    Route::middleware("auth:api")->get("/all/dates", [ActivityController::class, "index_dates"]);
    Route::middleware("auth:api")->get("/search-routine/dates/{query}", [ActivityController::class, "search_r_dates"]);
    Route::middleware("auth:api")->get("/search-date/dates/{query}", [ActivityController::class, "search_d_dates"]);
    Route::middleware("auth:api")->post("/weight/add", [WeightActivityController::class, "store"]);
    Route::middleware("auth:api")->get("/weight/delete/{id}", [WeightActivityController::class, "destroy"]);
    Route::middleware("auth:api")->post("/feeling/add", [FeelingActivityController::class, "store"]);
    Route::middleware("auth:api")->get("/feeling/delete/{id}", [FeelingActivityController::class, "destroy"]);
    Route::middleware("auth:api")->get("/routine/{id}", [RoutineActivityController::class, "show"]);
    Route::middleware("auth:api")->post("/routine/add", [RoutineActivityController::class, "store"]);
    Route::middleware("auth:api")->get("/routine/delete/{id}", [RoutineActivityController::class, "destroy"]);
    Route::middleware("auth:api")->get("/search-routine/{query}", [ActivityController::class, "search_r"]);
    Route::middleware("auth:api")->post("/date/search-routine/{query}", [ActivityController::class, "search_r_date"]);
    Route::middleware("auth:api")->get("/search-date/{query}", [ActivityController::class, "search_d_dates"]);
});

//Exercises
Route::prefix("/exercise")->group( function() {
    Route::middleware("auth:api")->get("/{id}", [ExerciseController::class, "index"]);
});