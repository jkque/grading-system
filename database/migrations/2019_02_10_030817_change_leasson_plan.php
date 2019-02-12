<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLeassonPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::rename('performances', 'performance_scores');

        Schema::table('lesson_plans', function (Blueprint $table) {
            $table->dropForeign('lesson_plans_subject_id_foreign');
            $table->dropColumn('subject_id');
            $table->dropForeign('lesson_plans_grading_period_id_foreign');
            $table->dropColumn('grading_period_id');
        });

        Schema::create('performances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('lesson_plan_id');
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->index(['lesson_plan_id']);
            $table->foreign('lesson_plan_id')->references('id')->on('lesson_plans');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('performance_scores', function (Blueprint $table) {
            $table->dropForeign('performances_lesson_plan_id_foreign');
            $table->dropColumn('lesson_plan_id');
            $table->unsignedInteger('performance_id');
            $table->index(['performance_id']);
            $table->foreign('performance_id')->references('id')->on('performances');
        });

        Schema::create('subject_lesson_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_subject_id');
            $table->unsignedInteger('grading_period_id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('lesson_plan_id');
            $table->index('section_subject_id');
            $table->index('section_id');
            $table->index('grading_period_id');
            $table->index('lesson_plan_id');
            $table->foreign('section_subject_id')->references('id')->on('section_subjects');
            $table->foreign('grading_period_id')->references('id')->on('grading_periods');
            $table->foreign('section_id')->references('id')->on('sections');
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
        Schema::drop('subject_lesson_plans');
    }
}
