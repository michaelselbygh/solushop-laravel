<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockKeepingUnit extends Model
{
    public $incrementing = false;

    public function product()
    {
        return $this->belongsTo('App\Product', 'id', 'sku_product_id');
    }
}
