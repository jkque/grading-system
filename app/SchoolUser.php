<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SchoolUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
     protected $fillable = ['school_id','user_id','section_id','role','status'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
    */

    protected $casts = [
        'status' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo('App\Section');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function school()
    {
        return $this->belongsTo('App\School');
    }
}
