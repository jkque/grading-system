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
        $section_count = Auth::user()->sectionSubjects->groupBy('section_id')->count();
        $section_list =  Auth::user()->sectionSubjects->groupBy('section_id')->keys();
        $sections = Section::whereIn('id',$section_list)->with('students')->get();
        $student_count = 0;
        foreach ($sections  as $section) {
            $student_count += $section->students->count();
        }
        return response(['section_count' => $section_count, 'student_count', $student_count], 200);
    }
}
