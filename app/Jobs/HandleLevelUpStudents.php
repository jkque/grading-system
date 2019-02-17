<?php

namespace App\Jobs;

use App\School;
use Mail;
use App\Mail\ReadyForEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class HandleLevelUpStudents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $school, $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(School $school,$data)
    {
        $this->school = $school;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $school = $this->school;
            $data = $this->data;
            foreach ($school->gradeLevels as $gradeLevel) {
                foreach ($gradeLevel->sections as $section) {
                    foreach ($section->students as $schoolUser) {
                        $student = $schoolUser->user;
                        if($student->finalGrades->where('section_id', $section->id)->count() === $section->subjects->count()){
                            $total_final = round($student->finalGrades->where('section_id', $section->id)->sum('score') / $section->subjects->count());
                            if($total_final >= $school->passing_rate){
                                $school_user = $student->schools->where('grade_level_id',$gradeLevel->id)->where('section_id',$section->id)->where('role','student')->first();
                                $school_user->grade_level_id =  $school_user->gradeLevel->next_level->id;
                                $school_user->section_id = null;
                                $school_user->save();
                            }
                        }
                    }
                }
            }
            $queuedEmail = [
                'user_id' => $data->auth_id,
                'email' => $data->auth_email,
                'meta_key' => 'ready_for_enrollment',
                'school_id' => $school->id,
                'description' => "Your request has been successfully proccessed for school year {$school->activeSchoolYear()->start} - {$school->activeSchoolYear()->end}",
            ];
    
            Mail::to($data->auth_email)->send(
                new ReadyForEnrollment($queuedEmail)
            );
            Log::info("Students successfully enrolled to next grade");
        } catch (\Exception $ex) {
            Log::error("Laravel error: ". $ex->getMessage());
            return true;
        }
    }
}
