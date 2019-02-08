<?php

namespace App\Http\Controllers\School;

use App\School;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InfoController extends Controller
{
    /**
     * Update the schools's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, School $school)
    {
        $this->validate($request, [
            'name' => 'required',
            'address' => 'required',
            'contact_number' => 'required|numeric',
        ]);

        return tap($school->load('schoolYears','gradeLevels.subjects','gradingPeriods'))->update($request->only('name', 'address', 'contact_number'));
    }
}
