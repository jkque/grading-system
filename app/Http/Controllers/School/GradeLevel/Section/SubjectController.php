<?php

namespace App\Http\Controllers\School\GradeLevel\Section;

use App\SectionSubject;
use App\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubjectController extends Controller
{
    public function list(Section $section)
    {
        return $section->subjects->load('teacher');
    }
    
    public function update(Request $request, SectionSubject $sectionSubject)
    {
        $sectionSubject->user_id = $request->teacher_id;
        $sectionSubject->save();
        return $sectionSubject->section->subjects->load('teacher');
    }
}
