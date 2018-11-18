<?php
namespace App;
use Eloquent;

class Profile extends Eloquent {

    protected $hidden = [];
    //protected $primaryKey = 'id';
    protected $table = 'profiles';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
