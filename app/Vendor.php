<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Vendor extends Authenticatable
{
    use Notifiable;

    protected $guard = 'vendor';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'name', 
        'username', 
        'phone', 
        'alt_phone',
        'email', 
        'address', 
        'password', 
        'balance'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function products()
    {
        return $this->hasMany('App\Product', 'product_vid', 'id' );
    }

    public function subscription()
    {
        return $this->hasOne('App\VendorSubscription', 'vs_vendor_id', 'id' );
    }
}
