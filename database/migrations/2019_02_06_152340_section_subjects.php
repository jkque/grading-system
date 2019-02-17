<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SectionSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('subject_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->index(['section_id','subject_id','user_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('section_id')->references('id')->on('sections');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->timestamps();
        });

        Schema::create('user_performances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('performance_score_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('grading_period_id');
            $table->unsignedInteger('section_subject_id');
            $table->integer('score')->default(0);
            $table->boolean('manual')->default(false);
            $table->index(['user_id','performance_score_id']);
            $table->index('grading_period_id');
            $table->index('section_subject_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('performance_score_id')->references('id')->on('performance_scores');
            $table->foreign('grading_period_id')->references('id')->on('grading_periods');
            $table->foreign('section_subject_id')->references('id')->on('section_subjects');
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
        Schema::dropIfExists('section_subjects');
        Schema::dropIfExists('user_performances');
    }
}
