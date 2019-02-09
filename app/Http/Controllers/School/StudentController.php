<?php

namespace App\Http\Controllers\School;

use App\User;
use App\School;
use App\SchoolUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\HandleImportStudent;

class StudentController extends Controller
{
    public function list(School $school)
    {
        return $school->students;
    }

    public function create(Request $request, School $school)
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
        
        SchoolUser::create([
            'school_id' => $school->id,
            'user_id' => $user->id,
            'role' => $request->role,
            'grade_level_id' => $request->grade_level_id ?? null,
            'section_id' => $request->section_id ?? null
        ]);

        return $school->students;
    }

    /**
     * Destroy the Student.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(School $school, User $user)
    {
        SchoolUser::whereUserId($user->id)->whereSchoolId($school->id)->whereRole('student')->delete();
        return $school->students;
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
            'address' => 'required',
            'birthdate' => 'required',
        ]);
        
        tap($user)->update($request->only('first_name','last_name','address','birthdate'));
        
        if($request->filled('grade_level_id')){
            SchoolUser::whereUserId($user->id)->whereGradeLevelId($school->grade_level_id)->whereRole('student')->update(['grade_level_id' => $request->grade_level_id]);
        }

        if($request->filled('section_id')){
            SchoolUser::whereUserId($user->id)->whereSectionId($school->section_id)->whereRole('student')->update(['section_id' => $request->section_id]);
        }
        return $school->students;
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
