<?php

namespace App\Jobs;

use App\GradeLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class HandleLevelUpStudents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $gradeLevel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(GradeLevel $gradeLevel)
    {
        $this->gradeLevel = $gradeLevel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $gradeLevel = $this->gradeLevel;
            $school = $gradeLevel->school;
            // foreach ($gradeLevel as $gradeLevel) {
            //     foreach ($gradeLevel->sections as $section) {
            //         foreach ($section->students as $schoolUser) {
            //             $student = $schoolUser->user;
            //             if($student->finalGrades->where('section_id', $section->id)->count() === $section->subjects->count()){
            //                 $total_final = round($student->finalGrades->where('section_id', $section->id)->sum('score') / $section->subjects->count());
            //                 if($total_final >= $school->passing_rate){
            //                     $school_user = $student->schools->where('grade_level_id',$gradeLevel->id)->where('section_id',$section->id)->where('role','student')->first();
            //                     $school_user->grade_level_id =  $school_user->gradeLevel->next_level->id;
            //                     $school_user->section_id = null;
            //                     $school_user->save();
            //                 }
            //             }
            //         }
            //     }
            // }
            dd($gradeLevel);
        } catch (\Exception $ex) {
            Log::error("Laravel error: ". $ex->getMessage());
            return true;
        }
    }
}
