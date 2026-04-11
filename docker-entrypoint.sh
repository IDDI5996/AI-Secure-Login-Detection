set -e

# Create storage link (safe to run multiple times)
php artisan storage:link

# Run migrations (--force for production, ignore errors if already up-to-date)
php artisan migrate --force || true

# Start Apache in foreground
exec apache2-foreground