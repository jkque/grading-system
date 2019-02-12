<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUserPerformances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('user_performances', function (Blueprint $table) {
            $table->dropForeign('user_performances_performance_id_foreign');
            $table->dropColumn('performance_id');
            $table->unsignedInteger('performance_score_id');
            $table->index(['performance_score_id']);
            $table->foreign('performance_score_id')->references('id')->on('performance_scores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_performances', function (Blueprint $table) {
            //
        });
    }
}
