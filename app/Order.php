<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $incrementing = false;

    public function order_state(){
        return $this->hasOne('App\OrderState', 'id', 'order_state' );
    }

    public function order_items(){
        return $this->hasMany('App\OrderItem', 'oi_order_id', 'id' );
    }

    public function customer(){
        return $this->hasOne('App\Customer', 'id', 'order_customer_id' );
    }


}
