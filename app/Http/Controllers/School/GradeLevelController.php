<?php

namespace App\Http\Controllers\School;

use App\School;
use App\GradeLevel;
use App\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\HandleLevelUpStudents;

class GradeLevelController extends Controller
{
    public function list(School $school)
    {
        return $school->gradeLevels()->with('sections','students','teachers','subjects')->get();
    }

    /**
     * Update the grade level information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  GradeLevel $gradeLevel)
    {

        $this->validate($request, [
            'name' => 'required',
        ]);
        return tap($gradeLevel)->update($request->only('name'));
    }

    public function view(GradeLevel $gradeLevel)
    {
        return $gradeLevel->load('sections');
    }

    public function viewSection(Section $section)
    {
        return $section->load('adviser');
    }

    public function levelUpStudents(GradeLevel $gradeLevel)
    {
        $school = $gradeLevel->school;
        $is_last_period = $school->gradingPeriods->last()->id === $school->gradingPeriods->where('status',true)->id ? true : false;
        if($is_last_period){
            HandleLevelUpStudents::dispatch($gradeLevel);
            return response('Import has been processed in the background',202);
        }else{
            return response('Last grading period should be active',422);
        }
    }

}
