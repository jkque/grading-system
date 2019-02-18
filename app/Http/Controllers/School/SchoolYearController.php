<?php

namespace App\Http\Controllers\School;

use App\SchoolYear;
use App\School;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\HandleLevelUpStudents;
use Illuminate\Support\Facades\Auth;

class SchoolYearController extends Controller
{
    /**
     * Create new school year
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, School $school)
    {
        $data = $this->validate($request, [
            'start' => 'required',
            'end' => 'required',
        ]);
        $status = $school->schoolYears->where('status',true)->count() ? false : true;

        SchoolYear::create([
            'school_id' => $school->id,
            'start' => $data['start'],
            'end' => $data['end'],
            'status' => $status,
        ]);

        return $school->load('schoolYears','gradeLevels.subjects','gradingPeriods');
    }

    /**
     * Update the school year information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  SchoolYear $schoolYear)
    {

        $this->validate($request, [
            'start' => 'required',
            'end' => 'required',
        ]);

        $school_years = $schoolYear->school->schoolYears;
        // if($request->status){
        //     if($school_years->where('status',true)->isNotEmpty()){
        //         if($school_years->where('status',true)->where('id','!=',$schoolYear->id)->isNotEmpty()){
        //             return response(['success' => false, 'message' => 'There should only be one active school year'],422);
        //         }
        //     }
        // }
        tap($schoolYear)->update($request->only('start', 'end'));
        return $schoolYear->school->load('schoolYears','gradeLevels.subjects','gradingPeriods');
    }

    /**
     * Destroy the school year.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,  SchoolYear $schoolYear)
    {
        $school = $schoolYear->school;
        $school_years = $school->schoolYears;
        if($schoolYear->status){
            return response()->json(['success' => false, 'message' => 'There should be one active school year']);
        }
        $schoolYear->delete();

        return $school->load('schoolYears','gradeLevels.subjects','gradingPeriods');
    }

    /**
     * Destroy the school year.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function readyForEnrollment(SchoolYear $schoolYear)
    {
        $school = $schoolYear->school;
        $school->schoolYears()->where('id','!=',$schoolYear->id)->update(['status' => false]);
        $schoolYear->status = true;
        $schoolYear->save();
        $grading_period = $school->gradingPeriods->first();
        $grading_period->status = true;
        $grading_period->save();
        $school->gradingPeriods()->where('id','!=',$grading_period->id)->update(['status' => false]);
        $data = new \stdClass;
        $data->auth_id = Auth::id();
        $data->auth_email = Auth::user()->email;
        HandleLevelUpStudents::dispatch((object)$school,$data);
        return $school->load('schoolYears','gradeLevels.subjects','gradingPeriods');
    }

}
