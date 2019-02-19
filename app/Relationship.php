<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','student_id','verified'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['verified' => 'boolean'];

    public function student()
    {
        return $this->belongsTo('App\User','student_id');
    }

    public function guardian()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
