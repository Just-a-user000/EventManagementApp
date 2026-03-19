@echo off
title Event Management App - Launcher
color 0A

echo.
echo ========================================
echo   EVENT MANAGEMENT APP - LAUNCHER
echo ========================================
echo.
echo   Questo script avviera' tutti i servizi
echo   necessari per l'applicazione:
echo.
echo   1. WebSocket Server (porta 8080)
echo   2. Laravel Scheduler (task automatici)
echo   3. Laravel Development Server (porta 8000)
echo.
echo ========================================
echo.

cd /d "%~dp0"

REM Verifica che Node.js sia installato
where node >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERRORE] Node.js non trovato!
    echo Scarica e installa Node.js da: https://nodejs.org/
    echo.
    pause
    exit /b 1
)

REM Verifica che PHP sia installato
where php >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERRORE] PHP non trovato!
    echo Assicurati che PHP sia installato e nel PATH.
    echo.
    pause
    exit /b 1
)

REM Verifica che Composer sia stato eseguito
if not exist "vendor\" (
    echo [ATTENZIONE] Cartella vendor non trovata.
    echo Eseguo: composer install
    echo.
    composer install
    if %errorlevel% neq 0 (
        echo [ERRORE] Composer install fallito!
        pause
        exit /b 1
    )
)

REM Verifica file .env
if not exist ".env" (
    echo [ATTENZIONE] File .env non trovato.
    echo Copio .env.example in .env...
    copy .env.example .env
    echo.
    echo Genero chiave applicazione...
    php artisan key:generate
    echo.
)

REM Crea cartelle storage se mancanti
if not exist "storage\framework\sessions\" mkdir storage\framework\sessions
if not exist "storage\framework\views\" mkdir storage\framework\views
if not exist "storage\framework\cache\" mkdir storage\framework\cache
if not exist "storage\framework\cache\data\" mkdir storage\framework\cache\data
if not exist "storage\logs\" mkdir storage\logs

echo ========================================
echo   AVVIO SERVIZI
echo ========================================
echo.

REM [1/3] WebSocket Server
echo [1/3] Avvio WebSocket Server...
cd websocket-server

REM Installa dipendenze npm se necessario
if not exist "node_modules\" (
    echo        Installazione dipendenze npm...
    call npm install --silent
)

REM Avvia WebSocket in una nuova finestra
start "WebSocket Server - Porta 8080" cmd /k "node server.js"
cd ..
timeout /t 3 /nobreak >nul
echo        [OK] WebSocket Server avviato
echo.

REM [2/3] Laravel Scheduler
echo [2/3] Avvio Laravel Scheduler...
start "Laravel Scheduler - Task Automatici" cmd /k "php artisan schedule:work"
timeout /t 2 /nobreak >nul
echo        [OK] Laravel Scheduler avviato
echo.

REM [3/3] Laravel Development Server
echo [3/3] Avvio Laravel Development Server...
start "Laravel Server - http://localhost:8000" cmd /k "php artisan serve"
timeout /t 2 /nobreak >nul
echo        [OK] Laravel Server avviato
echo.

echo ========================================
echo   TUTTI I SERVIZI SONO ATTIVI!
echo ========================================
echo.
echo   WebSocket Server:  http://localhost:8080
echo   Laravel App:       http://localhost:8000
echo   Laravel Scheduler: Attivo in background
echo.
echo ========================================
echo.
echo   ISTRUZIONI:
echo.
echo   - Apri il browser su: http://localhost:8000
echo   - Le notifiche real-time sono attive
echo   - I reminder automatici sono schedulati
echo   - La mappa si aggiorna ogni 30 secondi
echo.
echo   Per fermare i servizi, chiudi le finestre
echo   dei comandi che si sono aperte.
echo.
echo ========================================
echo.
echo   Premi un tasto per aprire il browser...
pause >nul

REM Apri il browser automaticamente
start http://localhost:8000

echo.
echo   Browser aperto! Buon lavoro!
echo.
echo   Premi un tasto per chiudere questo launcher...
pause >nul
