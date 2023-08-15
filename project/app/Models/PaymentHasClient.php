<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHasClient extends Model
{
    use HasFactory;

    protected $table = 'payment_has_clients';

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }

    public function details()
    {
        return $this->belongsTo('App\Models\PaymentSetup', 'payment_setup_id');
    }

    public function entry()
    {
        return $this->hasOne('App\Models\PaymentEntry', 'subscription_id', 'id');
    }
}
