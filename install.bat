@echo off
title Installing PROJECT-HOUSING System
color 0A

echo ================================================
echo    Installing PROJECT-HOUSING Dependencies
echo ================================================
echo.

echo [1/2] Installing Node.js dependencies...
echo This may take a few minutes. Please wait...
echo.

cd /d "C:\xampp\htdocs\PROJECT-HOUSING-main\socket-server"

if exist "package.json" (
    echo Found package.json. Running npm install...
    call npm install
) else (
    echo [ERROR] package.json not found in socket-server folder
    pause
    exit /b 1
)

if errorlevel 1 (
    echo.
    echo [ERROR] Failed to install dependencies
    echo Please make sure Node.js is installed.
    echo Download Node.js from: https://nodejs.org/
    pause
    exit /b 1
)

echo.
echo [OK] Dependencies installed successfully.

echo.
echo [2/2] Setup complete!
echo.
echo ================================================
echo    INSTALLATION SUCCESSFUL!
echo ================================================
echo.
echo You can now run run.bat to start the system.
echo.
pause