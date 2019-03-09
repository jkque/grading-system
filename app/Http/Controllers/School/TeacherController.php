<?php

namespace App\Http\Controllers\School;

use App\User;
use App\School;
use App\SchoolUser;
use App\LessonPlan;
use App\Performance;
use App\PerformanceScore;
use App\UserPerformance;
use App\Grade;
use App\SubjectLessonPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\HandleImportTeacher;
use Illuminate\Support\Facades\Auth;
use App\SectionSubject;
use App\FinalGrade;
use App\GradingPeriod;
use App\Section;

class TeacherController extends Controller
{
    public function list(School $school)
    {
        return $school->teachers;
    }

    public function create(Request $request, School $school)
    {
        
        $data = $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'mobile_number' => 'required|numeric',
            'address' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'birthdate' => 'required',
        ]);

        $user =  User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $request->middle_name,
            'mobile_number' => $data['mobile_number'],
            'address' => $data['address'],
            'email' => $data['email'],
            'birthdate' => $data['birthdate'],
            'password' => bcrypt($data['first_name'].''.$data['last_name']),
        ]);

        $user->assignRole('teacher');
        
        SchoolUser::create([
            'school_id' => $school->id,
            'user_id' => $user->id,
            'role' => $request->role,
            'grade_level_id' => $request->grade_level_id ?? null,
            'section_id' => $request->section_id ?? null
        ]);

        return $school->teachers;
    }

    /**
     * Destroy the Student.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(School $school, User $user)
    {
        SchoolUser::whereUserId($user->id)->whereSchoolId($school->id)->whereRole('teacher')->delete();
        return $school->teachers;
    }

    /**
     * Update the student information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, School $school,  User $user)
    {
        $data = $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required|numeric',
            'address' => 'required',
            'birthdate' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        if($request->filled('password')){
            $this->validate($request, [
                'password' => 'required|min:6',
            ]);
        }

        tap($user)->update($request->only('first_name', 'last_name', 'mobile_number', 'address', 'email','birthdate','middle_name'));

        $user->update([
            'password' => bcrypt($request->password),
        ]);

        if($request->grade_level_id && $request->section_id){
            $section = Section::whereId($request->section_id)->first();
            if($section->adviser){
                if($section->gradeLevel->id == $request->grade_level_id && $section->adviser->id != $user->id){
                    $errors = new \stdClass;
                    $errors->grade_level_id = ["The grade level and section is already taken by other teacher."];
                    $errors->section_id = ["The grade level and section is already taken by other teacher."];
                    return response(['errors' => $errors, 'message' => 'The given data was invalid.'],422);
                }
            }
        }
        
        if($request->filled('grade_level_id')){
            SchoolUser::whereUserId($user->id)->whereRole('teacher')->update(['grade_level_id' => $request->grade_level_id]);
        }

        if($request->filled('section_id')){
            Section::whereId($request->section_id)->update(['user_id' => $user->id]);
            SchoolUser::whereUserId($user->id)->whereRole('teacher')->update(['section_id' => $request->section_id]);
        }
        return $school->teachers;
    }

    /**
     * Import teacher.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        HandleImportTeacher::dispatch((object)$request->all());
        return response('Import has been processed in the background. Refresh to update',202);
    }

    /**
     * Get teacher setion and subject.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getSectionSubject(Request $request)
    {
        return Auth::user()->sectionSubjects->load(['section.students','subject.GradeLevel','lessonPlan' => function ($query) use($request){
            $query->where('grading_period_id',$request->grading_period_id);
        }]);
    }

    /**
     * Get teacher setions.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getSections(Request $request)
    {
        $section_list = Auth::user()->sectionSubjects->groupBy('section_id')->keys();
        $sections = Section::whereIn('id',$section_list)->with(['gradeLevel','gradeLevel.subjects','students','students.user.grades' => function($q) use($section_list){
            $q->whereIn('section_id',$section_list);
        },'students.user.finalGrades' => function($q) use($section_list){
            $q->whereIn('section_id',$section_list);
        },'students.user.grades.subject'],'students.user.finalGrades.subject')->get();
        return $sections;
    }

    /**
     * create lesson plan.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createLessonPlan(Request $request)
    {
        $performance_sample = [
            [
                'name' => 'Homework',
                'percent' => 0.20,
            ],
            [
                'name' => 'Quiz',
                'percent' => 0.30,
            ],
            [
                'name' => 'Exam',
                'percent' => 0.50,
            ],  
        ];

        $data = $this->validate($request, [
            'name' => 'required',
        ]); 
        
        $lessonPlan = LessonPlan::create([
            'name' => $data['name'],
            'user_id' => Auth::id()
        ]);

        foreach ($performance_sample as $key => $performance) {
            $perf = Performance::create([
                'name' => $performance['name'],
                'percentage' => $performance['percent'],
                'lesson_plan_id' => $lessonPlan->id,
            ]);

            if($perf->name == 'Exam'){
                PerformanceScore::create([
                    'score' => 50,
                    'performance_id' => $perf->id
                ]);
            }else{
                for ($i=1; $i <= 5; $i++) { 
                    PerformanceScore::create([
                        'score' => 10,
                        'performance_id' => $perf->id
                    ]);
                }
            }
        }

        return Auth::user()->lessonPlans->load('performances.performanceScore');
    }

    /**
     * update lesson plan.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateLessonPlan(Request $request, LessonPlan $lessonPlan)
    {
        $data = $this->validate($request, [
            'name' => 'required',
        ]);

        tap($lessonPlan)->update($request->only('name'));

        return Auth::user()->lessonPlans->load('performances.performanceScore');
    }

    /**
     * Destroy the lesson plan.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroyLessonPlan(LessonPlan $lessonPlan)
    {
        $lessonPlan->delete();
        return Auth::user()->lessonPlans->load('performances.performanceScore');
    }

    /**
     * Get teacher setion and subject.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function lessonPlanList(Request $request)
    {
        return Auth::user()->lessonPlans->load('performances.performanceScore');
    }

    /**
     * craft teachers lesson plan.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function craftLessonPlan(Request $request)
    { 
        $performance_sample = [
            [
                'name' => 'Homework',
                'percent' => 0.20,
            ],
            [
                'name' => 'Quiz',
                'percent' => 0.30,
            ],
            [
                'name' => 'Exam',
                'percent' => 0.50,
            ],  
        ];

        $lessonPlan = LessonPlan::create([
            'name' => 'Default',
            'user_id' => Auth::id()
        ]);

        foreach ($performance_sample as $key => $performance) {
            $perf = Performance::create([
                'name' => $performance['name'],
                'percentage' => $performance['percent'],
                'lesson_plan_id' => $lessonPlan->id,
            ]);

            if($perf->name == 'Exam'){
                PerformanceScore::create([
                    'score' => 50,
                    'performance_id' => $perf->id
                ]);
            }else{
                for ($i=1; $i <= 5; $i++) { 
                    PerformanceScore::create([
                        'score' => 10,
                        'performance_id' => $perf->id
                    ]);
                }
            }
        }
        return Auth::user()->lessonPlans->load('performances.performanceScore');
    }

    /**
     * update performance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updatePerformance(Request $request, Performance $performance)
    {
        $data = $this->validate($request, [
            'name' => 'required',
            'percentage' => 'required',
        ]);
        $checked = Performance::whereLessonPlanId($performance->lesson_plan_id)->where('id','!=',$performance->id)->get()->sum('percentage')+($data['percentage']/100);
        if($checked > 1){
            $errors = new \stdClass;
            $errors->percentage = ["The total percentage is over 100%."];
            return response(['errors' => $errors, 'message' => 'The given data was invalid.'],422);

        }
        $performance->name = $request->name;
        $performance->percentage = $request->percentage/100;
        $performance->save();
        return Auth::user()->lessonPlans->load('performances.performanceScore');
    }

    /**
     * Destroy the performance.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroyPerformance(Performance $performance)
    {
        $performance->delete();
        return Auth::user()->lessonPlans->load('performances.performanceScore');
    }

    public function createPerformance(Request $request)
    {
        
        $data = $this->validate($request, [
            'name' => 'required',
            'percentage' => 'required',
        ]);
        $checked = Performance::whereLessonPlanId($request->lesson_plan_id)->get()->sum('percentage')+($data['percentage']/100);
        if($checked > 1){
            $errors = new \stdClass;
            $errors->percentage = ["The total percentage is over 100%."];
            return response(['errors' => $errors, 'message' => 'The given data was invalid.'],422);

        }
        $perf = Performance::create([
            'name' => $data['name'],
            'percentage' => $data['percentage']/100,
            'lesson_plan_id' => $request->lesson_plan_id,
        ]);

        foreach ($request->inputs as $key => $value) {
            PerformanceScore::create([
                'score' => $request->inputs[$key]['score'],
                'performance_id' => $perf->id
            ]);
        }
        return Auth::user()->lessonPlans->load('performances.performanceScore');

    }

    /**
     * update performance scores.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updatePerformanceScores(Request $request)
    {
        foreach ($request->inputs as $key => $value) {
            if(isset($request->inputs[$key]['id'])){
                $score = PerformanceScore::find($request->inputs[$key]['id']);
                $score->score = $request->inputs[$key]['score'];
                $score->save();
            }else{
                PerformanceScore::create([
                    'score' => $request->inputs[$key]['score'],
                    'performance_id' => $request->id
                ]);
            }
        }
        return Auth::user()->lessonPlans->load('performances.performanceScore');
    }

    /**
     * update section subject lesson plan.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateSectionSubjectLessonPlan(Request $request)
    {
        $plan = SubjectLessonPlan::whereLessonPlanId($request->lesson_plan_id)
            ->whereGradingPeriodId($request->grading_period_id)
            ->whereSectionId($request->section_id)
            ->whereSectionSubjectId($request->id)
            ->first();
        if(!$plan){
            $plan = SubjectLessonPlan::create([
                'section_subject_id' => $request->id,
                'grading_period_id' => $request->grading_period_id,
                'section_id' => $request->section_id,
                'lesson_plan_id' => $request->lesson_plan_id,
            ]);
            $students = $plan->section->students;
            foreach ($students as $student) {
                foreach ($plan->lessonPlan->performances as $performance) {
                    foreach ($performance->PerformanceScore as $perfscore) {
                        $checked = UserPerformance::whereUserId($student->user->id)
                        ->whereGradingPeriodId($request->grading_period_id)->whereSectionSubjectId($request->id)->wherePerformanceScoreId($perfscore->id)->first();
                        if(!$checked){
                            UserPerformance::create([
                                'grading_period_id' => $request->grading_period_id,
                                'performance_score_id' => $perfscore->id,
                                'user_id' => $student->user->id,
                                'section_subject_id' => $request->id
                            ]);
                        }
                    }
                }
            }
        }
        return Auth::user()->sectionSubjects->load(['section.students','subject.GradeLevel','lessonPlan' => function ($query) use($request){
            $query->where('grading_period_id',$request->grading_period_id);
        }]);
    }

    /**
     * add user performances
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserPerformances(Request $request)
    {
        foreach ($request->users as $key => $value) {
            foreach ($request->users[$key]['user']['performances'] as $key2 => $perf) {
                $user_perf = UserPerformance::find($request->users[$key]['user']['performances'][$key2]['id']);
                $user_perf->score = $request->users[$key]['user']['performances'][$key2]['score'];
                $user_perf->save();
            }
        }
        return response('Students performances has been updated', 200);
    }

    /**
     * get user performances
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getUserPerformances(Request $request)
    {
        $plan = SubjectLessonPlan::whereSectionSubjectId($request->section_subject_id)->whereGradingPeriodId($request->grading_period_id)->first();
        if($plan){
            $perf_scores_list = $plan->lessonPlan->performances->where('id',$request->performance_id)->load('performanceScore')->first()->performanceScore->pluck('id');
            return $plan->section->students
            ->load(['user','user.performances' => function ($query) use($perf_scores_list,$request){
                $query->whereIn('performance_score_id', $perf_scores_list)->where('grading_period_id',$request->grading_period_id)->whereSectionSubjectId($request->section_subject_id);
            }]);
        }else{
            return [];
        }
    }

    /**
     * update user grades
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserGrades(Request $request)
    {
        $plan = SubjectLessonPlan::whereId($request->subject_lesson_plan_id)->whereGradingPeriodId($request->grading_period_id)->first();
        $school = GradingPeriod::find($request->grading_period_id)->school;
        $is_last_period = $school->gradingPeriods->last()->id == $request->grading_period_id ? true : false;
        $school_year = $school->schoolYears->where('status',true)->first();
        $section = $plan->section;
        if($plan){
            $subject = $plan->subject->subject;
            $students = User::whereIn('id',$request->student_list)->get();
            foreach ($students as $student) {
                $total_grade = 0;
                foreach ($plan->lessonPlan->performances as $performance) {
                    $perf_scores_list = $performance->performanceScore->pluck('id');
                    $total_points  = $performance->performanceScore->sum('score');
                    $students_performances = $student->performances->whereIn('performance_score_id',$perf_scores_list)->where('grading_period_id',$request->grading_period_id)
                    ->where('section_subject_id',$request->section_subject_id);
                    $student_points = $students_performances->sum('score');
                    $grade = ( ( $student_points / $total_points) * $performance->percentage) * 100;
                    $total_grade += $grade;
                }
                Grade::updateOrCreate(
                    ['user_id' => $student->id, 'grading_period_id' => $request->grading_period_id, 'section_id' => $plan->section_id, 'subject_id' => $subject->id],
                    ['score' => $total_grade]
                );
                if($is_last_period){
                    $final_grade = $student->grades->whereIn('grading_period_id',$school->gradingPeriods->pluck('id'))->where('subject_id',$subject->id)->sum('score') / $school->gradingPeriods->count();
                    FinalGrade::updateOrCreate(
                        ['user_id' => $student->id, 'school_year_id' => $school_year->id, 'section_id' => $plan->section_id, 'subject_id' => $subject->id],
                        ['score' => $final_grade]
                    );
                }
            }
            return $students->load(['grades' => function ($query) use($subject,$plan){
                $query->where('section_id',$plan->section_id)->where('subject_id',$subject->id);
            },'grades.gradingPeriod']);
        }else{
            return [];
        }
    }

    /**
     * update grades comment
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateGradesComment(Request $request)
    {
        foreach ($request->users as $key => $value) {
            foreach ($request->users[$key]['grades'] as $key2 => $grade) {
                $grade = Grade::find($request->users[$key]['grades'][$key2]['id']);
                $grade->comment = $request->users[$key]['grades'][$key2]['comment'];
                $grade->save();
            }
        }
        return response('Students grades has been updated', 200);
    }

    /**
     * get dashboard report
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dashboardReport(Request $request)
    {
        $section_count = Auth::user()->sectionSubjects->groupBy('section_id')->count();
        $section_list =  Auth::user()->sectionSubjects->groupBy('section_id')->keys();
        $sections = Section::whereIn('id',$section_list)->with('students')->get();
        $student_count = 0;
        foreach ($sections  as $section) {
            $student_count += $section->students->count();
        }
        return response(['section_count' => $section_count, 'student_count' => $student_count], 200);
    }

}
