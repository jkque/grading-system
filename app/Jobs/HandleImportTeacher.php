<?php

namespace App\Jobs;

use App\School;
use App\SchoolUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HandleImportTeacher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $request = $this->request;
        $school = School::find($request->school_id);
        foreach ((object)$request->users as $user) {
            $u =  User::create([
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'mobile_number' => $user['mobile_number'],
                'address' => $user['address'],
                'email' => $user['email'],
                'birthdate' => Carbon::parse($user['birthdate'])->format('Y-m-d'),
                'password' => bcrypt($user['first_name'].''.$user['last_name']),
            ]);
            $u->assignRole('teacher');
            
            SchoolUser::create([
                'school_id' => $school->id,
                'user_id' => $u->id,
                'role' => 'teacher',
                'grade_level_id' => $request->grade_level_id ?? null,
                'section_id' => $request->section_id ?? null
            ]);
        }

    }
}
