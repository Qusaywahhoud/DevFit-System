#!/bin/sh
php artisan migrate --force

# telegram listener, restarted automatically if it crashes
( while true; do php artisan telegram:listen; sleep 5; done ) &

# scheduler (runs telegram:send-exercise per routes/console.php)
php artisan schedule:work &

# web server keeps the container alive
exec php artisan serve --host=0.0.0.0 --port=8000