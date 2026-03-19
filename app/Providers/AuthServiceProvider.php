<?php

namespace App\Providers;

use App\Models\Event;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Le mappature delle policy per l'applicazione.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Event::class => EventPolicy::class,
    ];

    /**
     * Registra i servizi di autenticazione / autorizzazione.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
