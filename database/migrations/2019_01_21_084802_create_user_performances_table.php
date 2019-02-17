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
    }
}
