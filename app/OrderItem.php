<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $incrementing = false;

    public function order_item_state(){
        return $this->hasOne('App\OrderItemsState', 'id', 'oi_state' );
    }

    public function sku(){
        return $this->hasOne('App\StockKeepingUnit', 'id', 'oi_sku' );
    }

    public function order(){
        return $this->hasOne('App\Order', 'id', 'oi_order_id' );
    }

}

