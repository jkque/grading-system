<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolYear extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
    */
    protected $fillable = ['school_id','start','end','status'];

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
