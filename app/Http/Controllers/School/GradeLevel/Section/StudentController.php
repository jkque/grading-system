<?php

namespace App\Http\Controllers\School\GradeLevel\Section;

use App\GradeLevel;
use App\Section;
use App\School;
use App\SchoolUser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\HandleImportStudent;

class StudentController extends Controller
{
    public function list(Section $section)
    {
        return $section->students;
    }

    public function create(Request $request, Section $section)
    {
        
        $data = $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'birthdate' => 'required',
        ]);

        $user =  User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $request->middle_name,
            'address' => $data['address'],
            'birthdate' => $data['birthdate'],
        ]);
        
        $user->assignRole('student');


        
        SchoolUser::create([
            'school_id' => $section->gradeLevel->school->id,
            'user_id' => $user->id,
            'role' => $request->role,
            'grade_level_id' => $section->gradeLevel->id,
            'section_id' => $request->section_id ?? null
        ]);

        return $section->students;
    }

    /**
     * Destroy the Student.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Section $section, User $user)
    {
        SchoolUser::whereUserId($user->id)->whereSectionId($section->id)->whereRole('student')->delete();
        return $section->students;
    }

    /**
     * Update the student information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Section $section,  User $user)
    {

        $data = $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'birthdate' => 'required',
        ]);
        
        tap($user)->update($request->only('first_name','last_name','address','birthdate','middle_name'));
        SchoolUser::whereUserId($user->id)->whereSectionId($section->id)->whereRole('student')->update(['section_id' => $request->section_id]);
        return $section->students;
    }

    /**
     * Import student.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        HandleImportStudent::dispatch((object)$request->all());
        return response('Import has been processed in the background',202);
    }

}
