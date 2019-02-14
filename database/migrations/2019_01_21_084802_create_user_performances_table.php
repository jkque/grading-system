<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPerformancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        

        Schema::create('performance_scores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('performance_id');
            $table->integer('score');
            $table->integer('passing_score')->nullable();
            $table->index(['performance_id']);
            $table->integer('passing_score_percentage')->nullable();
            $table->foreign('performance_id')->references('id')->on('performances');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_performances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('performance_score_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('grading_period_id');
            $table->integer('score')->default(0);
            $table->boolean('manual')->default(false);
            $table->index(['user_id','performance_score_id']);
            $table->index('grading_period_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('performance_score_id')->references('id')->on('performance_scores');
            $table->foreign('grading_period_id')->references('id')->on('grading_periods');
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
        Schema::dropIfExists('performance_scores');
        Schema::dropIfExists('user_performances');
    }
}
