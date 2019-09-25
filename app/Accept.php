<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accept extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function option()
    {
        return $this->belongsTo('App\Option');
    }
}
