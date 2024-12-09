<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/api/action/new-job',
        '/spg/callbackM',
        '/f/l/get-percentage',
        '/ajax-flight-list',
        '/hajjpg/returnurl'
    ];
}
