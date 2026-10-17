@echo off
setlocal
cd /d "%~dp0"
echo ================================================
echo GOF3-light - XAMPP Setup
echo ================================================
echo.

if exist "C:\xampp\php\php.exe" (
  set "PHP=C:\xampp\php\php.exe"
) else (
  where php >nul 2>&1
  if errorlevel 1 (
    echo ERROR: PHP was not found. Install/start XAMPP first.
    pause
    exit /b 1
  )
  set "PHP=php"
)

where composer >nul 2>&1
if errorlevel 1 (
  echo WARNING: Composer was not found in PATH.
  echo The application requires Composer packages for PDF/QR/mail features.
  echo Install Composer for Windows, then run SETUP.bat again.
  echo.
) else (
  echo Installing PHP dependencies...
  composer install --no-interaction
  if errorlevel 1 (
    echo ERROR: Composer installation failed.
    pause
    exit /b 1
  )
)

echo.
echo Installing database and Super Admin...
"%PHP%" setup.php
if errorlevel 1 (
  echo.
  echo Database setup failed. Make sure MySQL is running in XAMPP.
  pause
  exit /b 1
)

echo.
echo ================================================
echo Setup complete.
echo Open: http://localhost/GOF3-light/public/
echo Email: admin@gof.org.ng
echo Password: Admin@1234
echo ================================================
pause
