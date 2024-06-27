<?php

namespace App;

use App\MasterData\Country;
use App\MasterData\Employee;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','lfcl_id','cont_id'
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function employee()
    {
        return Employee::on($this->country()->cont_conn)->where('aemp_lued', $this->id)->first();
    }
    public function country()
    {
        return (new Country())->country($this->cont_id);
       // return Country::where('id', $this->cont_id)->first();
    }
    public function countryDB()
    {
        return (new Country())->currentUserDB($this->cont_id);
       // return Country::where('id', $this->cont_id)->first();
    }
}
