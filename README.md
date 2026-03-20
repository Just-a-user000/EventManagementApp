# Event Management App

Applicazione web per la gestione di eventi sviluppata con **Laravel 11** e **Blade Templates**.

## Descrizione

Sistema completo per la gestione di eventi pubblici che permette agli utenti di cercare, visualizzare su mappa interattiva e iscriversi agli eventi. Include un'area personale per la gestione delle iscrizioni e una dashboard amministrativa per la creazione e gestione completa degli eventi.

## Funzionalità Principali

- Ricerca e visualizzazione eventi con filtri avanzati
- Mappa interattiva con Leaflet.js e geocoding automatico
- Sistema di iscrizione/disiscrizione con vincoli
- Dashboard personale "I Miei Eventi"
- Notifiche automatiche (promemoria, scadenze)
- Pannello amministrativo con statistiche

## Tecnologie

- **Backend**: Laravel 11, PHP 8.2+, MySQL
- **Frontend**: Blade Templates, Bootstrap 5, Leaflet.js
- **Pattern**: MVC (Model-View-Controller)
- **API**: Nominatim (OpenStreetMap) per geocoding

## Installazione Rapida

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Credenziali di Test

- **Admin**: admin@events.com / password
- **Utente**: mario@example.com / password

## Documentazione Completa

Per la documentazione dettagliata, architettura del sistema, diagrammi UML e analisi completa del progetto, consulta la **tesina**.

## Repository

GitHub: [Just-a-user000/EventManagementApp](https://github.com/Just-a-user000/EventManagementApp)

## Autore

**Nicolò Bettoni** - [@Just-a-user000](https://github.com/Just-a-user000)

---

📄 **Per maggiori dettagli tecnici e analisi approfondita, fare riferimento alla tesina del progetto.**
