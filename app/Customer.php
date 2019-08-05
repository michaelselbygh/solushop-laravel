<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use Notifiable;

    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 
        'first_name', 
        'last_name', 
        'email', 
        'email_verified',
        'phone', 
        'phone_verified', 
        'activation_code', 
        'password',
        'default_address', 
        'date_of_birth', 
        'milkshake', 
        'icono', 
        'sm'
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

    public function product_reviews()
    {
        return $this->hasMany('App\ProductReview', 'pr_customer_id', 'id' );
    }

    public function milk()
    {
        return $this->hasOne('App\Milk', 'id', 'id' );
    }

    public function chocolate()
    {
        return $this->hasOne('App\Chocolate', 'id', 'id' );
    }

    public function wishlist()
    {
        return $this->hasMany('App\WishlistItem', 'wi_customer_id', 'id' );
    }

    public function cart()
    {
        return $this->hasMany('App\CartItem', 'ci_customer_id', 'id' );
    }

    public function addresses()
    {
        return $this->hasMany('App\CustomerAddress', 'ca_customer_id', 'id' );
    }

    public function orders()
    {
        return $this->hasMany('App\Order', 'order_customer_id', 'id' );
    }

}
