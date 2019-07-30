<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorSubscription extends Model
{

    public function vendor()
    {
        return $this->hasOne('App\Vendor', 'id', 'vs_vendor_id');
    }
}
