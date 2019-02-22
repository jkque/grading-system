<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;
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
    
    public function lessonPlan()
    {
        return $this->belongsTo('App\LessonPlan');
    } 
}
