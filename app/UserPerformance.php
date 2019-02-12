<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPerformance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','score','manual','performance_score_id','grading_period_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function performanceScore()
    {
        return $this->belongsTo('App\PerformanceScore');
    }
     
}
