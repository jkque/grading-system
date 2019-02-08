<?php

namespace App\Http\Controllers\School;

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
}
