<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QueuedEmail extends Model
{
    protected $fillable = ['user_id', 'email','meta_key','meta_value','file_name','data','status','school_id','description'];
    protected $casts = ['run' => "boolean"];
    
    /**
     * Return data
     */
    public function getDataAttribute()
    {
        $data = json_decode($this->attributes['data']);
        return $data ? $data : null;
    }
}
