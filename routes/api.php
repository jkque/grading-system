<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });
    Route::get('user/school', 'School\FetchController@getByUser');

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');
    
    Route::post('school/register', 'School\RegisterController');
    Route::patch('school/{school}/update-info', 'School\InfoController@update');

    Route::patch('school/year/{schoolYear}/update', 'School\SchoolYearController@update');
    Route::post('school/year/{school}/create', 'School\SchoolYearController@create');
    Route::delete('school/year/{schoolYear}/destroy', 'School\SchoolYearController@destroy');

    Route::get('school/{school}/student/list', 'School\StudentController@list');
    Route::post('school/{school}/student/create', 'School\StudentController@create');
    Route::delete('school/{school}/student/{user}/destroy', 'School\StudentController@destroy');
    Route::patch('school/{school}/student/{user}/update', 'School\StudentController@update');
    Route::post('school/student/import', 'School\StudentController@import');

    Route::get('school/{school}/teacher/list', 'School\TeacherController@list');
    Route::post('school/{school}/teacher/create', 'School\TeacherController@create');
    Route::delete('school/{school}/teacher/{user}/destroy', 'School\TeacherController@destroy');
    Route::patch('school/{school}/teacher/{user}/update', 'School\TeacherController@update');
    Route::post('school/teacher/import', 'School\TeacherController@import');

    Route::patch('school/grading-period/{gradingPeriod}/setStatus', 'School\GradingPeriodController@setStatus');
    Route::patch('school/grading-period/{gradingPeriod}/update', 'School\GradingPeriodController@update');

    Route::get('school/{school}/grade-level/list', 'School\GradeLevelController@list');
    Route::patch('school/grade-level/{gradeLevel}/update', 'School\GradeLevelController@update');
    Route::get('school/grade-level/{gradeLevel}/view', 'School\GradeLevelController@view');

    Route::get('school/grade-level/{gradeLevel}/section/list', 'School\GradeLevel\SectionController@list');
    Route::post('school/grade-level/{gradeLevel}/section/create', 'School\GradeLevel\SectionController@create');
    Route::delete('school/section/{section}/destroy', 'School\GradeLevel\SectionController@destroy');
    Route::patch('school/section/{section}/update', 'School\GradeLevel\SectionController@update');

    Route::get('school/grade-level/{gradeLevel}/subject/list', 'School\GradeLevel\SubjectController@list');
    Route::post('school/grade-level/{gradeLevel}/subject/create', 'School\GradeLevel\SubjectController@create');
    Route::delete('school/subject/{subject}/destroy', 'School\GradeLevel\SubjectController@destroy');
    Route::patch('school/subject/{subject}/update', 'School\GradeLevel\SubjectController@update');

    Route::get('school/grade-level/{gradeLevel}/student/list', 'School\GradeLevel\StudentController@list');
    Route::post('school/grade-level/{gradeLevel}/student/create', 'School\GradeLevel\StudentController@create');
    Route::delete('school/grade-level/{gradeLevel}/student/{user}/destroy', 'School\GradeLevel\StudentController@destroy');
    Route::patch('school/grade-level/{gradeLevel}/student/{user}/update', 'School\GradeLevel\StudentController@update');

    Route::get('school/grade-level/{gradeLevel}/teacher/list', 'School\GradeLevel\TeacherController@list');
    Route::post('school/grade-level/{gradeLevel}/teacher/create', 'School\GradeLevel\TeacherController@create');
    Route::delete('school/grade-level/{gradeLevel}/teacher/{user}/destroy', 'School\GradeLevel\TeacherController@destroy');
    Route::patch('school/grade-level/{gradeLevel}/teacher/{user}/update', 'School\GradeLevel\TeacherController@update');

    Route::get('school/grade-level/section/{section}/student/list', 'School\GradeLevel\Section\StudentController@list');
    Route::post('school/grade-level/section/{section}/student/create', 'School\GradeLevel\Section\StudentController@create');
    Route::delete('school/grade-level/section/{section}/student/{user}/destroy', 'School\GradeLevel\Section\StudentController@destroy');
    Route::patch('school/grade-level/section/{section}/student/{user}/update', 'School\GradeLevel\Section\StudentController@update');
    Route::get('school/grade-level/section/{section}/view', 'School\GradeLevelController@viewSection');

    Route::patch('section-subject/{sectionSubject}/update', 'School\GradeLevel\Section\SubjectController@update');
    Route::get('school/grade-level/section/{section}/subject/list', 'School\GradeLevel\Section\SubjectController@list');

    Route::get('teacher/sections/list', 'School\TeacherController@getSectionSubject');

    Route::get('teacher/lesson-plans/list', 'School\TeacherController@lessonPlanList');
    Route::post('teacher/lesson-plans/craft', 'School\TeacherController@craftLessonPlan');
    Route::post('teacher/lesson-plans/create', 'School\TeacherController@createLessonPlan');
    Route::delete('teacher/lesson-plans/{lessonPlan}/destroy', 'School\TeacherController@destroyLessonPlan');
    Route::patch('teacher/lesson-plans/{lessonPlan}/update', 'School\TeacherController@updateLessonPlan');
    Route::patch('teacher/performance/{performance}/update', 'School\TeacherController@updatePerformance');
    Route::delete('teacher/performance/{performance}/destroy', 'School\TeacherController@destroyPerformance');
    Route::post('teacher/performance/create', 'School\TeacherController@createPerformance');
    Route::patch('teacher/performanceScore/update', 'School\TeacherController@updatePerformanceScores');
    Route::post('teacher/section-subject/lesson-plan/update', 'School\TeacherController@updateSectionSubjectLessonPlan');
    Route::post('student/performance/update', 'School\TeacherController@updateUserPerformances');
    Route::get('student/performance/list', 'School\TeacherController@getUserPerformances');
    Route::post('student/grades/update', 'School\TeacherController@updateUserGrades');
    Route::post('student/grades/updateComment', 'School\TeacherController@updateGradesComment');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});
