<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('grading_period_id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('subject_id');
            $table->decimal('score', 5, 2)->default(0.00);
            $table->string('comment');
            $table->index(['grading_period_id','section_id','subject_id','user_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('grading_period_id')->references('id')->on('grading_periods');
            $table->foreign('section_id')->references('id')->on('sections');
            $table->foreign('subject_id')->references('id')->on('subjects');
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('grades');
    }
}
