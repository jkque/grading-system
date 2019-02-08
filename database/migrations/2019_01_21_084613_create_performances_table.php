<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerformancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_plan_id');
            $table->integer('score');
            $table->integer('passing_score')->nullable();
            $table->index(['lesson_plan_id']);
            $table->integer('passing_score_percentage')->nullable();
            $table->foreign('lesson_plan_id')->references('id')->on('lesson_plans');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('performances');
    }
}
