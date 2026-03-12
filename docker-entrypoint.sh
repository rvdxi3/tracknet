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
if [ "${FORCE_RESEED}" = "true" ]; then
    echo "    FORCE_RESEED=true — dropping and recreating all tables..."
    php artisan migrate:fresh --seed --force
else
    php artisan migrate --force

    echo "==> Seeding database..."
    USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1 | tr -d '\r\n')
    if [ "$USER_COUNT" = "0" ]; then
        echo "    No users found — running all seeders..."
        php artisan db:seed --force
    else
        echo "    Users exist (count=$USER_COUNT) — checking products..."
        PRODUCT_COUNT=$(php artisan tinker --execute="echo \App\Models\Product::count();" 2>/dev/null | tail -1 | tr -d '\r\n')
        if [ "$PRODUCT_COUNT" = "0" ]; then
            echo "    No products found — running data seeders..."
            php artisan db:seed --class=CategoriesTableSeeder --force
            php artisan db:seed --class=ProductsTableSeeder --force
            php artisan db:seed --class=InventoryTableSeeder --force
            php artisan db:seed --class=SuppliersTableSeeder --force
            php artisan db:seed --class=PurchaseOrdersTableSeeder --force
            php artisan db:seed --class=OrdersTableSeeder --force
        else
            echo "    Products exist (count=$PRODUCT_COUNT) — skipping seed."
        fi
    fi
fi

if [ "${CLEAN_TEST_USERS}" = "true" ]; then
    echo "==> CLEAN_TEST_USERS=true — removing non-staff user accounts..."
    php artisan tinker --execute="App\Models\User::whereNotIn('role', ['admin','inventory','sales'])->delete(); echo 'Done.';" 2>&1
fi

echo "==> Starting Apache..."
exec apache2-foreground
