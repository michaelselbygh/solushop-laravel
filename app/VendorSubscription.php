<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorSubscription extends Model
{

    public function vendor()
    {
        return $this->hasOne('App\Vendor', 'id', 'vs_vendor_id');
    }

    public function package()
    {
        return $this->hasOne('App\VSPackage', 'id', 'vs_vsp_id');
    }
}
