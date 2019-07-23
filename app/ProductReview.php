<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    
    public function customer()
    {
        return $this->belongsTo('App\Customer', 'id', 'pr_customer_id');
    }
}
