<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public function state()
    {
        return $this->hasOne('App\CouponState', 'id', 'coupon_state' );
    }
}
