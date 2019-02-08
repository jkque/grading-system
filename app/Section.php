<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['grade_level_id','name','user_id'];
    
    public function subjects()
    {
        return $this->hasMany('App\SectionSubject')->with('subject');
    }

    public function adviser()
    {
        return $this->belongsTo('App\User');
    }

    public function gradeLevel()
    {
        return $this->belongsTo('App\GradeLevel');
    }

    public function students()
    {
        return $this->hasMany('App\SchoolUser')->where('role', 'student')->with('user');
    }
}
