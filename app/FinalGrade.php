<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinalGrade extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','school_year_id','section_id','subject_id','score','comment'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function schoolYear()
    {
        return $this->belongsTo('App\SchoolYear');
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
