<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentKhalti extends Model
{
    protected $table = 'payment_khalti';

    protected $fillable = [
        'uuid', 'status', 'pre_token', 'verified_token'
    ];

}
