<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
