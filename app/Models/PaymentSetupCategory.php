<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetupCategory extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $table = 'payment_setup_categories';

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}
