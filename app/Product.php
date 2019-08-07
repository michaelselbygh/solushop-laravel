<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $incrementing = false;
    public function images()
    {
        return $this->hasMany('App\ProductImage', 'pi_product_id', 'id' );
    }

    public function skus()
    {
        return $this->hasMany('App\StockKeepingUnit', 'sku_product_id', 'id' )->orderBy('sku_variant_description');
    }

    public function category()
    {
        return $this->hasMany('App\ProductCategory', 'id', 'product_cid' );
    }

    public function reviews()
    {
        return $this->hasMany('App\ProductReview', 'pr_product_id', 'id' );
    }

    public function vendor()
    {
        return $this->belongsTo('App\Vendor', 'product_vid', 'id');
    }

    public function state()
    {
        return $this->hasOne('App\ProductState', 'id', 'product_state');
    }
}
