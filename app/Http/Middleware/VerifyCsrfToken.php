<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'pay/nibl/confirm',
        'hbl-payment/Lrot3yy3CQkJnBipexF4BiKD5ybWsfKjWzAfG6clilSYw0WWuh78ITJNUplA9Dzo',
        'hbl-payment-response',
    ];
}
