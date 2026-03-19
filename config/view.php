<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Percorsi di Archiviazione delle Viste
    |--------------------------------------------------------------------------
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Percorso delle Viste Compilate
    |--------------------------------------------------------------------------
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
