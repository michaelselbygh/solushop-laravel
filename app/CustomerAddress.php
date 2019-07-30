<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    public function shipping_fare(){
        return $this->hasOne('App\ShippingFare', 'sf_town', 'ca_town' );
    }
}
