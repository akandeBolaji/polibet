<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    protected $hidden = ['reference', 'transaction_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
