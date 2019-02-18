<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonPlan extends Model
{
    use SoftDeletes;
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $fillable = ['name','user_id'];

    public function performances()
    {
        return $this->hasMany('App\Performance');
    }

    public function techer()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function subjects()
    {
        return $this->hasMany('App\SubjectLessonPlan','lesson_plan_id');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleted(function($lessonPlan) {
            $lessonPlan->subjects()->delete();
        });
        
        
    }
    
}
