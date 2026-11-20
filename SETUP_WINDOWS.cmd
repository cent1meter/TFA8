@echo off
setlocal
cd /d "%~dp0"
php -v
if errorlevel 1 goto :failed
php -r "exit(PHP_VERSION_ID >= 80200 ? 0 : 1);"
if errorlevel 1 (
    echo PHP 8.2 or newer is required.
    goto :failed
)
call composer install
if errorlevel 1 goto :failed
if not exist .env copy .env.example .env >nul
php artisan config:clear
if errorlevel 1 goto :failed
php -r "exit(preg_match('/^APP_KEY=.+$/m', file_get_contents('.env')) ? 0 : 1);"
if errorlevel 1 (
    php artisan key:generate
    if errorlevel 1 goto :failed
)
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
if errorlevel 1 goto :failed
php artisan migrate --force
if errorlevel 1 goto :failed
php artisan storage:link
if errorlevel 1 goto :failed
php artisan optimize:clear
if errorlevel 1 goto :failed
echo.
echo Setup complete. Optional demo data: php artisan db:seed
echo Start with: php artisan serve
echo Then open http://127.0.0.1:8000/register to create your login.
exit /b 0
:failed
echo.
echo Setup stopped. Fix the error above before starting the application.
exit /b 1
