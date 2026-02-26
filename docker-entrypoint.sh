#!/bin/bash
set -e

# Run migrations safely
php artisan migrate --force || true

# Start Apache in foreground
exec apache2-foreground
