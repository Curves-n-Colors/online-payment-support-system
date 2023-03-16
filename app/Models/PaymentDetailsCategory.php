<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetailsCategory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'payment_details_categories';

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}
