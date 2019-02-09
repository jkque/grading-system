<?php

namespace App\Http\Controllers\School;

use App\User;
use App\School;
use App\SchoolUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\HandleImportTeacher;

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
            'mobile_number' => $data['mobile_number'],
            'address' => $data['address'],
            'email' => $data['email'],
            'birthdate' => $data['birthdate'],
            'password' => bcrypt($data['first_name'].''.$data['last_name']),
        ]);
        
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
                'password' => 'required|confirmed|min:6',
            ]);
        }

        tap($user)->update($request->only('first_name', 'last_name', 'mobile_number', 'address', 'email','birthdate'));

        $user->update([
            'password' => bcrypt($request->password),
        ]);
        
        if($request->filled('grade_level_id')){
            SchoolUser::whereUserId($user->id)->whereGradeLevelId($school->grade_level_id)->whereRole('teacher')->update(['grade_level_id' => $request->grade_level_id]);
        }

        if($request->filled('section_id')){
            SchoolUser::whereUserId($user->id)->whereSectionId($school->section_id)->whereRole('teacher')->update(['section_id' => $request->section_id]);
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
        return response('Import has been processed in the background',202);
    }


}
