<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    public function sku()
    {
        return $this->hasOne('App\StockKeepingUnit', 'id', 'ci_sku' );
    }
}
