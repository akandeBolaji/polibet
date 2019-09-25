<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class ussd_user extends Model {

    protected $table = 'rapidussd_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['difficulty_level','name', 'notify','office_id','phone','email','session','progress','confirm_from','menu_item_id'];


    public function savedAddress()
    {
        return $this->hasMany('App\savedAddress');
    }

    public function bitcoinWallet()
    {
        return $this->hasOne('App\bitcoinWallet');
    }

    public function funds()
    {
        return $this->hasMany('App\Fund');
    }

    public function account()
    {
        return $this->hasOne('App\Account');
    }


}
