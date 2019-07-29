<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockKeepingUnit extends Model
{
    public $incrementing = false;

    public function product()
    {
        return $this->hasOne('App\Product', 'id', 'sku_product_id');
    }

    public function product_images()
    {
        return $this->hasMany('App\ProductImage', 'pi_product_id', 'sku_product_id');
    }
}
