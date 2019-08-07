<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageFlag extends Model
{
    public function message(){
        return $this->hasOne('App\Message', "id", 'mf_mid' );
    }
}
