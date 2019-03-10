<?php

namespace App;

use Carbon\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;
    use SoftDeletes;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'middle_name', 'address', 'mobile_number', 'birthdate', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'photo_url', 'name','age'
    ];

    /**
     * Get the profile photo URL attribute.
     *
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
    }

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        if($this->middle_name){
            return ucfirst($this->last_name).', '.ucwords($this->first_name).' '.ucfirst($this->middle_name[0]).'.';
        }else{
            return ucfirst($this->last_name).', '.ucwords($this->first_name);
        }
    }

    /**
     * Get the user's age.
     *
     * @return string
     */
    public function getAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age;
    }

    public function schools()
    {
        return $this->hasMany('App\SchoolUser')->with('school');
    }

    public function ownSchool()
    {
        return $this->hasOne('App\School');
    }

    public function advisedSection()
    {
        return $this->hasMany('App\Section');
    }

    public function sectionSubjects()
    {
        return $this->hasMany('App\SectionSubject');
    }

    public function lessonPlans()
    {
        return $this->hasMany('App\LessonPlan');
    }

    public function performances()
    {
        return $this->hasMany('App\UserPerformance');
    }

    public function grades()
    {
        return $this->hasMany('App\Grade');
    }

    public function finalGrades()
    {
        return $this->hasMany('App\FinalGrade');
    }

    public function guardians()
    {
        return $this->hasMany('App\Relationship','student_id')->with('guardian');
    }

    public function children()
    {
        return $this->hasMany('App\Relationship','user_id')->with('student');
    }
}
