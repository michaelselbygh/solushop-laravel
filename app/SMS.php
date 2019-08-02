<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SMS extends Model
{
    protected $table = 'sms';

    public function state()
    {
        return $this->hasOne('App\SMSState', 'id', 'sms_state' );
    }
}
