@echo off
title Kedarnath Spices & Herbs - Local Dev Server
color 0A
cls
echo ============================================================
echo   🌿 KEDARNATH SPICES & HERBS - LOCAL DEVELOPMENT SERVER 🌿
echo ============================================================
echo.
echo   [+] Starting PHP Local Server on http://localhost:8081 ...
echo   [+] Opening browser...
echo.
echo   Press Ctrl+C to stop the server at any time.
echo ============================================================
echo.

timeout /t 2 /nobreak >nul
start http://localhost:8081
php -S 127.0.0.1:8081 -t "%~dp0"
pause
