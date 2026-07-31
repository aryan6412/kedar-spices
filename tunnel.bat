@echo off
title Kedarnath Spices & Herbs - Public Tunnel (kedarspices)
color 0B
cls
echo ============================================================
echo   🌐 KEDARNATH SPICES & HERBS - CUSTOM SUBDOMAIN TUNNEL 🌐
echo ============================================================
echo.
echo   [1/2] Starting PHP Local Server on port 8081...
start /B php -S 127.0.0.1:8081 -t "%~dp0" >nul 2>&1

timeout /t 2 /nobreak >nul

echo   [2/2] Launching Custom Subdomain Tunnel: kedarspices
echo.
echo   ----------------------------------------------------------
echo   Custom Domain: https://kedarspices.loca.lt
echo.
echo   [!] Keep this window OPEN while sharing your website.
echo   [!] Press Ctrl+C at any time to stop the tunnel.
echo   ----------------------------------------------------------
echo.

npx -y localtunnel --port 8081 --local-host 127.0.0.1 --subdomain kedarspices
pause
