<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Determina se l'utente può visualizzare qualsiasi evento
     */
    public function viewAny(?User $user): bool
    {
        return true; // Tutti possono visualizzare la lista eventi
    }

    /**
     * Determina se l'utente può visualizzare l'evento
     */
    public function view(?User $user, Event $event): bool
    {
        // Gli eventi pubblicati possono essere visualizzati da chiunque
        if ($event->isPublished()) {
            return true;
        }

        // Gli eventi in bozza possono essere visualizzati solo dagli admin
        return $user && $user->isAdmin();
    }

    /**
     * Determina se l'utente può creare eventi
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determina se l'utente può aggiornare l'evento
     */
    public function update(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determina se l'utente può eliminare l'evento
     */
    public function delete(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }
}
