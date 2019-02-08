<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = ['grade_level_id', 'name'];

    public function gradeLevel()
    {
        return $this->belongsTo('App\GradeLevel');
    }

    public function sections()
    {
        return $this->hasMany('App\SectionSubject')->with('section');
    }
}
