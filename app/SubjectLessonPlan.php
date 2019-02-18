<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectLessonPlan extends Model
{
    use SoftDeletes;
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $fillable = ['section_subject_id','grading_period_id','section_id','lesson_plan_id'];

    public function subject()
    {
        return $this->belongsTo('App\SectionSubject','section_subject_id')->with('subject');
    }

    public function gradingPeriod()
    {
        return $this->belongsTo('App\GradingPeriod');
    }

    public function section()
    {
        return $this->belongsTo('App\Section');
    }

    public function lessonPlan()
    {
        return $this->belongsTo('App\LessonPlan');
    }
}
