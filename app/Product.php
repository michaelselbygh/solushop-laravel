<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function product_images()
    {
        return $this->hasMany('App\ProductImage', 'pi_product_id', 'product_id' );
    }

    public function vendor()
    {
        return $this->belongsTo('App\Vendor', 'vendor_id', 'product_vid');
    }
}
