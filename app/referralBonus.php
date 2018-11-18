<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class referralBonus extends Model
{
    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
