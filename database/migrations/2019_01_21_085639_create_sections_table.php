<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('grade_level_id');
            $table->string('name');
            $table->unsignedInteger('user_id')->nullable();
            $table->index(['user_id','grade_level_id']);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('grade_level_id')->references('id')->on('grade_levels');
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
        Schema::dropIfExists('sections');
    }
}
