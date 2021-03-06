<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function option()
    {
        return $this->belongsTo('App\Option');
    }

    public function custom_bet()
    {
        return $this->belongsTo('App\customBet');
    }

}
