<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['school_id','level','name'];

    public function school()
    {
        return $this->belongsTo('App\School');
    }

    public function subjects()
    {
        return $this->hasMany('App\Subject');
    }

    public function sections()
    {
        return $this->hasMany('App\Section');
    }

    public function students()
    {
        return $this->hasMany('App\SchoolUser')->where('role', 'student')->with('user','section');
    }

    public function teachers()
    {
        return $this->hasMany('App\SchoolUser')->where('role', 'teacher')->with('user','section');
    }
}
