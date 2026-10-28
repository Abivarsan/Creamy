@echo off
title Creamy Desktop App Launcher
echo =======================================================
echo    Launching Creamy Artisan Ice Cream Desktop POS
echo =======================================================
echo.

cd /d "%~dp0"

echo [1/2] Checking build status...
if not exist "bin\Debug\net8.0-windows\Creamy.WPF.exe" (
    echo Building WPF application...
    dotnet build
    if errorlevel 1 (
        echo [ERROR] Build failed. Please review build errors.
        pause
        exit /b 1
    )
)

echo [2/2] Launching Creamy.WPF in foreground...
start "" "bin\Debug\net8.0-windows\Creamy.WPF.exe"

echo.
echo =======================================================
echo [OK] Creamy Desktop Application launched successfully!
echo You can keep this terminal open or close it at any time.
echo =======================================================
