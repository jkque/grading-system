<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','contact_number','user_id','address'];

    public function schoolYears()
    {
        return $this->hasMany('App\SchoolYear');
    }

    public function gradeLevels()
    {
        return $this->hasMany('App\GradeLevel');
    }

    public function gradingPeriods()
    {
        return $this->hasMany('App\gradingPeriod');
    }

    public function members()
    {
        return $this->hasMany('App\SchoolUser');
    }

    public function students()
    {
        return $this->members()->where('role', 'student')->with('user','section','gradeLevel');
    }

    public function teachers()
    {
        return $this->members()->where('role', 'teacher')->with('user','section','gradeLevel');
    }

    public function admins()
    {
        return $this->members()->where('role', 'school_admin')->with('user');
    }
}
