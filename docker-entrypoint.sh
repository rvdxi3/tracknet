#!/bin/bash
set -e

echo "==> Caching config and routes..."
php artisan config:cache
php artisan route:cache

echo "==> Compiling views (non-fatal)..."
php artisan view:cache 2>&1 || echo "[WARN] view:cache failed — views will be compiled on demand"

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Starting Apache..."
exec apache2-foreground
