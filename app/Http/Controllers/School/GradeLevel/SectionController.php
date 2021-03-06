<?php

namespace App\Http\Controllers\School\GradeLevel;

use App\GradeLevel;
use App\Section;
use App\User;
use App\SchoolUser;
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
        return $gradeLevel->sections()->with('adviser','students','subjects')->get();
        
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
        return $gradeLevel->sections()->with('adviser','students','subjects')->get();
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
        $user = User::find($request->user_id);
        if($user->advisedSection){
            $errors = new \stdClass;
            $errors->user_id = ["Teacher has already assigned section."];
            return response(['errors' => $errors, 'message' => 'The given data was invalid.'],422);

        }
        if($request->user_id){
            // $checked_teacher =  SchoolUser::whereUserId($request->user_id)->whereRole('teacher')->first();
            // if(!$checked_teacher){
                SchoolUser::whereUserId($request->user_id)->whereRole('teacher')->update(['grade_level_id' => $section->gradeLevel->id, 'section_id' =>  $section->id]);
            // }
            foreach ($section->gradeLevel->subjects as $subject) {
                if(SectionSubject::whereSectionId($section->id)->whereSubjectId($subject->id)->whereUserId(null)->first()){
                    SectionSubject::updateOrCreate(
                        ['section_id' => $section->id,'subject_id' => $subject->id],
                        ['user_id' => $request->user_id]
                    );
                }
            }
        }
        return $section->gradeLevel->sections()->with('adviser','students','subjects')->get();
    }
}
