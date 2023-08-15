<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;
    protected $table = 'payment_settings';

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
