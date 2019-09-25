<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dispute extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function option()
    {
        return $this->belongsTo('App\Option');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }
}
