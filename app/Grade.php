<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','grading_period_id','section_id','subject_id','score','comment'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function gradingPeriod()
    {
        return $this->belongsTo('App\GradingPeriod');
    }

    public function section()
    {
        return $this->belongsTo('App\Section');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subject');
    }

    
}
