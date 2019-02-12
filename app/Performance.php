<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Performance extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $fillable = ['name','lesson_plan_id','percentage'];

    public function lessonPlan()
    {
        return $this->belongsTo('App\LessonPlan');
    }

    public function performanceScore()
    {
        return $this->hasMany('App\PerformanceScore');
    }
}
