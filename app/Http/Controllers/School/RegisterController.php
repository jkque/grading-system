<?php

namespace App\Http\Controllers\School;

use App\School;
use App\SchoolUser;
use App\SchoolYear;
use App\GradeLevel;
use App\GradingPeriod;
use App\Rank;
use App\Subject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{

    /**
     * Create a new school instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    public function __invoke(Request $request)
    {
        $subjects = ['Science','English','Filipino','Math'];
        $data = $this->validate($request, [
            'name' => 'required|unique:schools',
            'address' => 'required',
            'contact_number' => 'required|numeric',
            'start' => 'required',
            'end' => 'required',
        ]);

        $school =  School::create([
            'name' => $data['name'],
            'address' => $data['address'],
            'contact_number' => $data['contact_number'],
            'user_id' => Auth::id(),
        ]);
        
        SchoolUser::create([
            'school_id' => $school->id,
            'user_id' => Auth::id(),
            'role' => 'school_admin',
        ]);

        $year = SchoolYear::create([
            'school_id' => $school->id,
            'start' => $data['start'],
            'end' => $data['end'],
            'status' => true,
        ]);

        for ($i=1; $i <= 12; $i++) { 
            GradeLevel::create([
                'school_id' => $school->id,
                'level' => $i,
                'name' => "Grade {$i}",
            ]);
        }

        foreach (GradeLevel::all() as $level) {
            foreach ($subjects as $key => $value) {
                Subject::create([
                    'grade_level_id' => $level->id,
                    'name' => $value,
                ]);
            }
        }

        for ($i=1; $i <= 4; $i++) {
            switch ($i) {
                case 1:
                    $name = '1st Grading';
                    break;
                case 2:
                    $name = '2nd Grading';
                    break;
                case 3:
                    $name = '3rd Grading';
                    break;
                case 4:
                    $name = '4th Grading';
                    break;
            }
            GradingPeriod::create([
                'school_id' => $school->id,
                'level' => $i,
                'name' => $name,
                'status' => $i === 1 ? true : false    
            ]);
        }    

        return $school->load('schoolYears','gradeLevels.subjects','gradingPeriods');
    }
}
