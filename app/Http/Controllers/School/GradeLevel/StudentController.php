<?php

namespace App\Http\Controllers\School\GradeLevel;

use App\GradeLevel;
use App\School;
use App\SchoolUser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\HandleImportStudent;

class StudentController extends Controller
{
    public function list(GradeLevel $gradeLevel)
    {
        return $gradeLevel->students;
    }

    public function create(Request $request, GradeLevel $gradeLevel)
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
            'address' => $data['address'],
            'birthdate' => $data['birthdate'],
        ]);
        
        $user->assignRole('student');


        
        SchoolUser::create([
            'school_id' => $gradeLevel->school->id,
            'user_id' => $user->id,
            'role' => $request->role,
            'grade_level_id' => $gradeLevel->id,
            'section_id' => $request->section_id ?? null
        ]);

        return $gradeLevel->students;
    }

    /**
     * Destroy the Student.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(GradeLevel $gradeLevel, User $user)
    {
        SchoolUser::whereUserId($user->id)->whereGradeLevelId($gradeLevel->id)->whereRole('student')->delete();
        return $gradeLevel->students;
    }

    /**
     * Update the student information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, GradeLevel $gradeLevel,  User $user)
    {

        $data = $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'birthdate' => 'required',
        ]);
        
        tap($user)->update($request->only('first_name','last_name','address','birthdate'));
        SchoolUser::whereUserId($user->id)->whereGradeLevelId($gradeLevel->id)->whereRole('student')->update(['section_id' => $request->section_id]);
        return $gradeLevel->students;
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
