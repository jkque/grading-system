<?php

namespace App\Http\Controllers\School;

use App\School;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FetchController extends Controller
{
    public function getByUser(Request $request)
    {
        $schools = Auth::user()->schools();
        if($schools->get()->isNotEmpty()){
            if($schools->get()->count() > 1){
                return $schools->with('school.schoolYears','school.gradeLevels.subjects','school.gradingPeriods')->get();
            }else{
                return $schools->with('school.schoolYears','school.gradeLevels.subjects','school.gradingPeriods')->first()->school;
            }
        }else{
            return null;
        }
    }

    /**
     * get dashboard report
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dashboardReport(Request $request)
    {
        $grade_level_count = Auth::user()->ownSchool->gradeLevels->count();
        $members_count = Auth::user()->ownSchool->members->count();
        $students_count = Auth::user()->ownSchool->students->count();
        $teachers_count = Auth::user()->ownSchool->teachers->count();
        return response(['grade_level_count' => $grade_level_count, 'members_count'=> $members_count, 'students_count'=> $students_count, 'teachers_count' => $teachers_count], 200);
    }
}
