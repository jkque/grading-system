<?php

namespace App\Http\Controllers\School\GradeLevel;

use App\GradeLevel;
use App\Section;
use App\SectionSubject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SectionController extends Controller
{
    public function list(GradeLevel $gradeLevel)
    {
        return $gradeLevel->sections()->with('adviser','students','subjects')->get();
    }

    public function create(Request $request, GradeLevel $gradeLevel)
    {

        foreach ($request->inputs as $key => $value) {
            $section = Section::create([
                'grade_level_id' => $gradeLevel->id,
                'name' => $request->inputs[$key]['name'],
                'user_id' => $request->inputs[$key]['teacher_id'] ?? null
            ]);

            foreach ($gradeLevel->subjects as $subject) {
                SectionSubject::create([
                    'section_id' => $section->id,
                    'subject_id' => $subject->id,
                ]);
            }
        }
        return $gradeLevel->sections;
        
    }

    /**
     * Destroy the section.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Section $section)
    {
        $gradeLevel = $section->gradeLevel;
        $section->delete();
        return $gradeLevel->sections;
    }

    /**
     * Update the section  information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  Section $section)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        tap($section)->update($request->only('name','teacher_id'));
        return $section->gradeLevel->sections->load('subjects','students','adviser');
    }
}
