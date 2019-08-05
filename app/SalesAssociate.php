<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SalesAssociate extends Authenticatable
{
    use Notifiable;


    protected $guard = 'sales-associate';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'first_name', 
        'last_name', 
        'email', 
        'phone',  
        'password',
        'address', 
        'badge', 
        'id_type', 
        'id_file', 
        'mode_of_payment',
        'payment_details',
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


    public function badge_info()
    {
        return $this->hasOne('App\SABadge', 'id', 'badge');
    }
}
