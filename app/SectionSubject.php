<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SectionSubject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['section_id','subject_id','user_id'];
    
    public function section()
    {
        return $this->belongsTo('App\Section');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }
    
    public function teacher()
    {
        return $this->belongsTo('App\User','user_id');
    }
    
    public function lessonPlan()
    {
        return $this->hasOne('App\SubjectLessonPlan')->with('lessonPlan');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleted(function($sectionSubject) {
            $sectionSubject->lessonPlan()->delete();
        });
        
    }
}
