<?php

namespace App\Http\Controllers\School;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GuardianController extends Controller
{
    public function list(Request $request, User $guardian)
    {
        return $guardian->children->load('student.grades','student.grades.subject','student.grades.section','student.grades.gradingPeriod','student.finalGrades','student.finalGrades.subject','student.finalGrades.section','student.finalGrades.schoolYear','student.schools','student.schools.gradeLevel','student.schools.gradeLevel.subjects');
    }
}
