<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class Option extends Model
{
    public function bet()
    {
        return $this->belongsTo('App\customBet');
    }

    public function bets()
    {
        return $this->hasMany('App\Bet');
    }

    public function userbets(){
        // $user = JWTAuth::parseToken()->authenticate();
         return $this->hasMany( 'App\Bet')->where('user_id', JWTAuth::parseToken()->authenticate()->id);
       }

    public function disputes()
    {
        return $this->hasMany('App\Dispute');
    }

    public function accepts()
    {
        return $this->hasMany('App\Accept');
    }

    public function userdispute(){
        // $user = JWTAuth::parseToken()->authenticate();
         return $this->hasMany( 'App\Dispute')->where('user_id', JWTAuth::parseToken()->authenticate()->id);
       }

    public function useraccept(){
    // $user = JWTAuth::parseToken()->authenticate();
        return $this->hasMany( 'App\Accept')->where('user_id', JWTAuth::parseToken()->authenticate()->id);
    }
}
