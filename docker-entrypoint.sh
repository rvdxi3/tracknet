#!/bin/bash
set -e

# -------------------------------------------------------
# Fix Apache MPM at runtime (avoids Docker layer caching).
# Delete ALL mpm_* symlinks and re-create ONLY mpm_prefork.
# mod_php requires prefork; event/worker cause AH00534.
# -------------------------------------------------------
echo "==> Fixing Apache MPM (ensuring only mpm_prefork is active)..."
find /etc/apache2/mods-enabled/ -name 'mpm_*' -delete 2>/dev/null || true
ln -sf /etc/apache2/mods-available/mpm_prefork.load \
       /etc/apache2/mods-enabled/mpm_prefork.load
[ -f /etc/apache2/mods-available/mpm_prefork.conf ] && \
    ln -sf /etc/apache2/mods-available/mpm_prefork.conf \
           /etc/apache2/mods-enabled/mpm_prefork.conf || true
echo "    Active MPM modules: $(ls /etc/apache2/mods-enabled/ | grep mpm | tr '\n' ' ')"

echo "==> Caching config and routes..."
php artisan config:cache
php artisan route:cache

echo "==> Compiling views (non-fatal)..."
php artisan view:cache 2>&1 || echo "[WARN] view:cache failed — views will compile on demand"

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Starting Apache..."
exec apache2-foreground
