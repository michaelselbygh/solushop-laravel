<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveredItem extends Model
{
    protected $table = 'delivered_items';

    public function order_item(){
        return $this->hasOne('App\OrderItem', 'id', 'di_order_item_id' );
    }
}
