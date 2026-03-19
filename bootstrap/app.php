<?php

/*
|--------------------------------------------------------------------------
| Crea l'Applicazione
|--------------------------------------------------------------------------
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Collega Interfacce Importanti
|--------------------------------------------------------------------------
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Restituisci l'Applicazione
|--------------------------------------------------------------------------
|
| Questo script restituisce l'istanza dell'applicazione. L'istanza viene
| fornita allo script chiamante in modo da poter separare la costruzione
| delle istanze dall'effettiva esecuzione dell'applicazione e invio delle risposte.
|
*/

return $app;
