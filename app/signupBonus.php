<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class signupBonus extends Model
{
    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
