<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PaymentDetail extends Model
{
    protected $table = 'payment_details';

    protected $fillable = [
        'payment_status'
    ];

    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'client_id');
    }

    public function setup()
    {
        return $this->belongsTo('App\Models\PaymentSetup', 'payment_setup_id')->withTrashed();
    }

    public function payment_nibl()
    {
        return $this->hasOne('App\Models\PaymentNibl', 'uuid', 'uuid');
    }

    public function payment_khalti()
    {
        return $this->hasOne('App\Models\PaymentKhalti', 'uuid', 'uuid');
    }

}
