<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * I nomi dei cookie che non devono essere crittografati.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
