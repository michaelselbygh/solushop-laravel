<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    public function product()
    {
        return $this->hasOne('App\Product', 'id', 'wi_product_id');
    }

    public function product_images()
    {
        return $this->hasMany('App\ProductImage', 'pi_product_id', 'wi_product_id');
    }
}
