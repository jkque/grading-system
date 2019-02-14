<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnGradingPeriodIdToUserPerformances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::disableForeignKeyConstraints();
        // Schema::table('user_performances', function (Blueprint $table) {
        //     $table->unsignedInteger('grading_period_id')->after('user_id');
        //     $table->index(['grading_period_id']);
        //     $table->foreign('grading_period_id')->references('id')->on('grading_periods');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::disableForeignKeyConstraints();
        // Schema::table('user_performances', function (Blueprint $table) {
        //     $table->dropForeign('user_performances_grading_period_id_foreign');
        //     $table->dropColumn('grading_period_id');
        // });
    }
}
