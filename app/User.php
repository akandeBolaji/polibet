<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'ip', 'email', 'password', 'phone', 'last_seen', 'status', 'full_name', 'user_name', 'referrer_id', 'refer_id', 'bet_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'activation_token', 'ip', 'phone', 'provider', 'provider_id', 'created_at', 'updated_at', 'refer_id', 'referrer_id'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public function profile()
    {
        return $this->hasOne('App\Profile');
    }

    public function disputes()
    {
        return $this->hasMany('App\Dispute');
    }

    public function accepts()
    {
        return $this->hasMany('App\Accept');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function bets()
    {
        return $this->hasMany('App\Bet');
    }

    public function customBets()
    {
        return $this->hasMany('App\customBet');
    }

    public function funds()
    {
        return $this->hasMany('App\Fund');
    }

    public function withdrawals()
    {
        return $this->hasMany('App\Withdrawal');
    }

    public function account()
    {
        return $this->hasOne('App\Account');
    }

    public function signupBonus()
    {
        return $this->hasOne('App\signupBonus');
    }

    public function referralBonus()
    {
        return $this->hasMany('App\referralBonus');
    }
}
