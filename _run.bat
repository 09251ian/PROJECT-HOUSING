@echo off
title PROJECT-HOUSING System Launcher
color 0A

echo ================================================
echo    PROJECT-HOUSING Real Estate System
echo ================================================
echo.

:: ========== CONFIGURATION ==========
set PROJECT_PATH=C:\xampp\htdocs\PROJECT-HOUSING-main
set MYSQL_BIN=C:\xampp\mysql\bin
set MYSQL_USER=root
set MYSQL_PASS=
set DB_NAME=housing
set SOCKET_PORT=3000

:: ========== CHECK XAMPP ==========
echo [1/5] Checking XAMPP services...

:: Check if Apache is running
netstat -an | findstr ":80 " >nul
if errorlevel 1 (
    echo [ERROR] Apache is not running!
    echo Please open XAMPP Control Panel and start Apache.
    pause
    exit /b 1
)
echo [OK] Apache is running.

:: Check if MySQL is running
netstat -an | findstr ":3306 " >nul
if errorlevel 1 (
    echo [ERROR] MySQL is not running!
    echo Please open XAMPP Control Panel and start MySQL.
    pause
    exit /b 1
)
echo [OK] MySQL is running.

:: ========== START WEBSOCKET SERVER ==========
echo [2/5] Starting WebSocket Server...

cd /d "%PROJECT_PATH%\socket-server"
start "WebSocket Server" cmd /k "echo WebSocket Server Running on Port %SOCKET_PORT% && npm start"

echo [OK] WebSocket server started on port %SOCKET_PORT%

:: ========== OPEN BROWSER ==========
echo [3/5] Opening browser...
timeout /t 3 /nobreak >nul
start "" "http://localhost/PROJECT-HOUSING-main/public"

:: ========== DISPLAY SUMMARY ==========
echo [4/5] System ready!
echo.
echo ================================================
echo    SYSTEM IS RUNNING!
echo ================================================
echo.
echo    Website: http://localhost/PROJECT-HOUSING-main
echo    WebSocket: ws://localhost:%SOCKET_PORT%
echo.
echo    Login with your registered account
echo.
echo ================================================
echo.
echo [5/5] To stop the server, close the WebSocket window
echo       or run the stop.bat file.
echo.
pause