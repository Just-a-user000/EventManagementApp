<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * Gli URI che devono essere raggiungibili mentre la modalità manutenzione è attiva.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
