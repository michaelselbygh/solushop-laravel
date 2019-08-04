<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickedUpItem extends Model
{
    protected $table = 'picked_up_items';

    public function order_item(){
        return $this->hasOne('App\OrderItem', 'id', 'pui_order_item_id' );
    }
}
