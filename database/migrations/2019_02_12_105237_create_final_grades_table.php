<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinalGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('final_grades', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('school_year_id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('subject_id');
            $table->decimal('score', 5, 2)->default(0.00);
            $table->string('comment')->nullable();
            $table->index(['school_year_id','section_id','subject_id','user_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('school_year_id')->references('id')->on('school_years');
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
        Schema::dropIfExists('final_grades');
    }
}
