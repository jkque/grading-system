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
        Schema::create('user_performances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('performance_id');
            $table->unsignedInteger('user_id');
            $table->decimal('score', 5, 2)->default(0.00);
            $table->boolean('manual')->default(false);
            $table->index(['user_id','performance_id']);
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('user_performances');
    }
}
