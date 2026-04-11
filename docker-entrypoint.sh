#!/bin/bash
set -e
php artisan storage:link
# Run migrations safely
php artisan migrate --force || true

# Start Apache in foreground
exec apache2-foreground
