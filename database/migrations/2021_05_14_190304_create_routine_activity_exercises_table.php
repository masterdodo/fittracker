<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoutineActivityExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routine_activity_exercises', function (Blueprint $table) {
            $table->id();
            $table->integer("amount");
            $table->string("unit")->nullable();
            $table->foreignId("exercise_id")->constrained();
            $table->foreignId("routine_activity_id")->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routine_activity_exercises');
    }
}
