<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerformanceScore extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $fillable = ['score','passing_score'.'passing_score_percentage','performance_id'];

    public function performance()
    {
        return $this->belongsTo('App\Performance');
    }

    public function userPerformance()
    {
        return $this->belongsTo('App\UserPerformance','performance_score_id')->with('user');
    }
}
