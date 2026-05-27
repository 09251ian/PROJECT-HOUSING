@echo off
title Stop PROJECT-HOUSING
color 0C

echo ================================================
echo    Stopping PROJECT-HOUSING System
echo ================================================
echo.

echo Stopping WebSocket server...
taskkill /F /IM node.exe 2>nul

if errorlevel 1 (
    echo WebSocket server was not running.
) else (
    echo WebSocket server stopped successfully.
)

echo.
echo Done.
pause