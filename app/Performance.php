<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Performance extends Model
{
    use SoftDeletes;
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
