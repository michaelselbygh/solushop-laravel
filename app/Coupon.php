<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public function state()
    {
        return $this->hasOne('App\CouponState', 'id', 'coupon_state' );
    }

    public function sales_associate(){
        return $this->hasOne('App\SalesAssociate', "email", 'coupon_owner' );
    }
}
