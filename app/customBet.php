<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JWTAuth;

class customBet extends Model
{
    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bets()
    {
        return $this->hasMany('App\Bet');
    }

    public function userbets(){
        // $user = JWTAuth::parseToken()->authenticate();
         return $this->hasMany( 'App\Bet')->where('user_id', JWTAuth::parseToken()->authenticate()->id);
       }

    public function options()
    {
        return $this->hasMany('App\Option');
    }
}
