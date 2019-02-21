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
                'user_id' => $request->inputs[$key]['user_id'] ?? null
            ]);

            foreach ($gradeLevel->subjects as $subject) {
                $checked = SectionSubject::whereSectionId($section->id)->whereSubjectId($subject->id)->first();
                if(!$checked){
                    SectionSubject::create([
                        'section_id' => $section->id,
                        'subject_id' => $subject->id,
                    ]);
                }
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
        $checked = $section->gradeLevel->sections->where('id','!=',$section->id)->where('name',$request->name);
        if($checked->isNotEmpty()){
            $errors = new \stdClass;
            $errors->name = ["The name field is already taken."];
            return response(['errors' => $errors, 'message' => 'The given data was invalid.'],422);
        }
        tap($section)->update($request->only('name','user_id')); 
        if($request->user_id){
            foreach ($section->gradeLevel->subjects as $subject) {
                SectionSubject::updateOrCreate(
                    ['section_id' => $section->id,'subject_id' => $subject->id],
                    ['user_id' => $request->user_id]
                );
            }
        }
        return $section->gradeLevel->sections()->with('adviser','students','subjects')->get();
    }
}
