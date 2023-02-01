<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use App\Models\Logs;

class Client extends Model
{
    protected $table = 'clients';

    public function payment_setups()
    {
        return $this->hasMany('App\Models\PaymentSetup', 'client_id');
    }

    public function payment_entries()
    {
        return $this->hasMany('App\Models\PaymentEntry', 'client_id');
    }

    public function payment_details()
    {
        return $this->hasMany('App\Models\PaymentDetail', 'client_id');
    }
}