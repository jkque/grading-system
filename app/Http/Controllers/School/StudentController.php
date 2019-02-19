<?php

namespace App\Http\Controllers\School;

use App\User;
use App\School;
use App\SchoolUser;
use App\Relationship;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\HandleImportStudent;

class StudentController extends Controller
{
    public function list(School $school)
    {
        return $school->students->load('user.guardians');
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
        
        $user->assignRole('student');


        
        SchoolUser::create([
            'school_id' => $school->id,
            'user_id' => $user->id,
            'role' => $request->role,
            'grade_level_id' => $request->grade_level_id ?? null,
            'section_id' => $request->section_id ?? null
        ]);

        return $school->students->load('user.guardians');
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
        return $school->students->load('user.guardians');
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
            SchoolUser::whereUserId($user->id)->whereSchoolId($school->id)->whereRole('student')->update(['grade_level_id' => $request->grade_level_id]);
        }

        if($request->filled('section_id')){
            SchoolUser::whereUserId($user->id)->whereSchoolId($school->id)->whereRole('student')->update(['section_id' => $request->section_id]);
        }
        return $school->students->load('user.guardians');
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

    public function addEditGuardian(Request $request, School $school)
    {
        if($request->id){
            $user = User::find($request->id);
        
            $data = $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'address' => 'required',
                'email' => 'required|email|unique:users,email,'.$user->id,
            ]);

            if($request->password){
                $this->validate($request, [
                    'password' => 'required|min:6',
                ]);
            }

            tap($user)->update($request->only('first_name','last_name','address','email'));

            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }else{
        
            $data = $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'address' => 'required',
                'email' => 'required|email|max:255|unique:users',
            ]);

            $user =  User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'address' => $data['address'],
                'email' => $data['email'],
                'password' => bcrypt($data['first_name'].''.$data['last_name']),
            ]);

            $user->assignRole('guardian');
    
            SchoolUser::create([
                'school_id' => $school->id,
                'user_id' => $user->id,
                'role' => 'guardian',
            ]);
    
            Relationship::create([
                'user_id' => $user->id,
                'student_id' => $request->student_id,
                'verified' => true,
            ]);
        }

        return $school->students->load('user.guardians');
    }

}
