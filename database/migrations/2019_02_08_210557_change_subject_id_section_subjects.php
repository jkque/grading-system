<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSubjectIdSectionSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('section_subjects', function (Blueprint $table) {
            $table->dropForeign('section_subjects_user_id_foreign');
            $table->dropColumn('user_id');
        });

        Schema::table('section_subjects', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->after('subject_id')->nullable();
            $table->index(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section_subjects', function (Blueprint $table) {
            $table->dropForeign('section_subjects_user_id_foreign');
            $table->dropColumn('user_id');
        });

        Schema::table('section_subjects', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->after('style_id');
            $table->index(['user_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
}
