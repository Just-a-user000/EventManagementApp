@echo off
echo ========================================
echo   Avvio WebSocket Server
echo ========================================
echo.

cd /d "%~dp0"

if not exist "node_modules\" (
    echo Installazione dipendenze npm...
    call npm install
    echo.
)

echo Avvio server WebSocket sulla porta 8080...
echo.
node server.js

pause
