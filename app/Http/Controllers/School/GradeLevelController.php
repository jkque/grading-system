<?php

namespace App\Http\Controllers\School;

use App\School;
use App\GradeLevel;
use App\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

}
