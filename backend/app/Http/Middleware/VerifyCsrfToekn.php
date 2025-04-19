<?php

namespace App\Http\Middleware;

// use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

// class VerifyCsrfToken extends BaseVerifier
// {
//     /**
//      * Indicates whether the XSRF-TOKEN cookie should be set on the response.
//      *
//      * @var bool
//      */
//     protected $addHttpCookie = true;

//     /**
//      * The URIs that should be excluded from CSRF verification.
//      *
//      * @var array
//      */


//     /**
//      * The URIs that should be excluded from CSRF verification.
//      *
//      * @var array
//      */
//     protected $except = [
//         // 'login/google/callback', // Add the Google callback URL to the CSRF exceptions
//         '/pay-via-ajax', '/success','/cancel','/fail','/ipn'
//     ];
// }

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/payment/ssl-success',
        '/payment/ssl-fail',
        '/payment/ssl-cancel',
        '/payment/ssl-ipn',

        // SSLCOMMERZ sandbox default redirects:
        'success',
        'fail',
        'cancel',
    ];
}
