@echo off
setlocal EnableExtensions
cd /d "%~dp0"

echo ================================================================
echo        GARKO ORPHANS FOUNDATION - GOF3-LIGHT
echo        One-click XAMPP installer and launcher
echo ================================================================
echo.

set "XAMPP=C:\xampp"
set "PHP=%XAMPP%\php\php.exe"
set "MYSQL=%XAMPP%\mysql\bin\mysqladmin.exe"
set "COMPOSER_PHAR=%~dp0tools\composer.phar"

if not exist "%PHP%" (
  echo ERROR: XAMPP PHP was not found at:
  echo %PHP%
  echo.
  echo Install XAMPP in C:\xampp and run this file again.
  pause
  exit /b 1
)

if not exist "%MYSQL%" (
  echo ERROR: XAMPP MySQL was not found at:
  echo %MYSQL%
  pause
  exit /b 1
)

rem Start XAMPP services if they are not already running.
echo Checking XAMPP MySQL...
"%MYSQL%" -h 127.0.0.1 -u root ping >nul 2>&1
if errorlevel 1 (
  echo MySQL is not running. Starting XAMPP services...
  if exist "%XAMPP%\xampp_start.exe" (
    start "XAMPP" "%XAMPP%\xampp_start.exe"
    timeout /t 5 /nobreak >nul
  ) else (
    echo Please start Apache and MySQL from XAMPP Control Panel.
  )
)

rem Check again.
"%MYSQL%" -h 127.0.0.1 -u root ping >nul 2>&1
if errorlevel 1 (
  echo ERROR: MySQL is not responding.
  echo Start MySQL in XAMPP Control Panel, then run this file again.
  pause
  exit /b 1
)

echo MySQL is running.

echo.
echo Checking Composer dependencies...
if not exist "%COMPOSER_PHAR%" (
  where composer >nul 2>&1
  if not errorlevel 1 (
    echo Composer found in PATH.
    composer install --no-interaction --no-dev --optimize-autoloader
    if errorlevel 1 goto composer_failed
  ) else (
    echo Composer was not found. Downloading Composer automatically...
    powershell -NoProfile -ExecutionPolicy Bypass -Command "try { Invoke-WebRequest -UseBasicParsing -Uri 'https://getcomposer.org/composer-stable.phar' -OutFile '%COMPOSER_PHAR%'; exit 0 } catch { Write-Host $_; exit 1 }"
    if errorlevel 1 goto composer_download_failed
  )
)

if exist "%COMPOSER%" (
  "%PHP%" "%COMPOSER_PHAR%" install --no-interaction --no-dev --optimize-autoloader
  if errorlevel 1 goto composer_failed
)

echo.
echo Installing GOF3-light database...
"%PHP%" setup.php
if errorlevel 1 goto setup_failed

if not exist "%XAMPP%\apache\bin\httpd.exe" (
  echo Apache executable was not found.
  pause
  exit /b 1
)

rem Start Apache if needed.
"%XAMPP%\apache\bin\httpd.exe" -t >nul 2>&1
if errorlevel 1 (
  echo WARNING: Apache configuration test failed. Check XAMPP Control Panel.
) else (
  powershell -NoProfile -Command "try { Invoke-WebRequest -UseBasicParsing -Uri 'http://localhost/' -TimeoutSec 2 | Out-Null; exit 0 } catch { exit 1 }" >nul 2>&1
  if errorlevel 1 (
    if exist "%XAMPP%\xampp_start.exe" start "XAMPP" "%XAMPP%\xampp_start.exe"
    timeout /t 5 /nobreak >nul
  )
)

echo.
echo ================================================================
echo GOF3-light setup is complete.
echo.
echo Open: http://localhost/GOF3-light/public/
echo Email: admin@gof.org.ng
echo Password: Admin@1234
echo ================================================================
echo.
start "" "http://localhost/GOF3-light/public/"
pause
exit /b 0

:composer_download_failed
echo.
echo ERROR: Could not download Composer automatically.
echo Check your internet connection and run this file again.
pause
exit /b 1

:composer_failed
echo.
echo ERROR: Composer dependency installation failed.
echo Make sure Apache/MySQL are running and PHP 8.x is installed.
pause
exit /b 1

:setup_failed
echo.
echo ERROR: Database setup failed.
echo Check the message above. Make sure MySQL is running and root has no password.
pause
exit /b 1
