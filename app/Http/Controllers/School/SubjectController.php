<?php

namespace App\Http\Controllers\School;

use App\Subject;
use App\GradeLevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubjectController extends Controller
{
    public function list(GradeLevel $gradeLevel)
    {
        return $gradeLevel->subjects;
    }

    public function create(Request $request, GradeLevel $gradeLevel)
    {

        foreach ($request->inputs as $key => $value) {
            $section = Subject::create([
                'grade_level_id' => $gradeLevel->id,
                'name' => $request->inputs[$key]['name'],
            ]);
        }
        return $gradeLevel->subjects;
        
    }

    /**
     * Destroy the section.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        $gradeLevel = $subject->gradeLevel;
        $subject->delete();
        return $gradeLevel->subjects;
    }

    /**
     * Update the section  information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  Subject $subject)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        tap($subject)->update($request->only('name'));
        return $subject->gradeLevel->subjects;
    }
}
