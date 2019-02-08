<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GradingPeriod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = ['school_id', 'level', 'name', 'status'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
    */
    protected $casts = [
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo('App\School');
    }
}
