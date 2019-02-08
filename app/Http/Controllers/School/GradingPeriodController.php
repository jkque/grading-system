<?php

namespace App\Http\Controllers\School;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GradingPeriod;

class GradingPeriodController extends Controller
{
    /**
     * Update the status grading period.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function setStatus(Request $request, GradingPeriod $gradingPeriod)
    {
        $school = $gradingPeriod->school;
        $grading_periods = $school->gradingPeriods;
        if(!$request->status){
            if($grading_periods->where('status',true)->count() === 1){
                return response('There should only be one active grading period', 422);
            }
        }else{
            $school->gradingPeriods()->where('id','!=',$gradingPeriod->id)->update(['status' => false]);
        }
        tap($gradingPeriod)->update($request->only('status'));
        return $school->load('schoolYears','gradeLevels.subjects','gradingPeriods');
    }

    /**
     * Update the grading period information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  GradingPeriod $gradingPeriod)
    {

        $this->validate($request, [
            'name' => 'required',
        ]);
        $school = $gradingPeriod->school;
        $grading_periods = $school->gradingPeriods;
        if(!$request->status){
            if($grading_periods->where('status',true)->count() === 1){
                return response(['success' => false, 'message' => 'There should only be one active grading period'], 422);
            }
        }else{
            $school->gradingPeriods()->where('id','!=',$gradingPeriod->id)->update(['status' => false]);
        }
        tap($gradingPeriod)->update($request->only('name','status'));
        return $gradingPeriod->school->load('schoolYears','gradeLevels.subjects','gradingPeriods');
    }
}
