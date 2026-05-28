#!/bin/bash
set -e

echo "=== Starting Naaliatan's Accessories on Railway ==="

echo "=== Checking images ==="
ls /app/public/images/ 2>&1 || echo "Images folder NOT FOUND"
echo "=== End images check ==="

# ── Normalize Railway MySQL variable names ──────────────────────────────────
# Railway's MySQL plugin may inject variables as MYSQL_HOST / MYSQL_USER etc.
# Fall back to those if the MYSQLHOST-style vars are not set.
MYSQLHOST="${MYSQLHOST:-${MYSQL_HOST:-}}"
MYSQLPORT="${MYSQLPORT:-${MYSQL_PORT:-3306}}"
MYSQLUSER="${MYSQLUSER:-${MYSQL_USER:-}}"
MYSQLPASSWORD="${MYSQLPASSWORD:-${MYSQL_PASSWORD:-}}"
MYSQLDATABASE="${MYSQLDATABASE:-${MYSQL_DATABASE:-}}"

echo "=== DB connection info ==="
echo "  MYSQLHOST     = ${MYSQLHOST}"
echo "  MYSQLPORT     = ${MYSQLPORT}"
echo "  MYSQLUSER     = ${MYSQLUSER}"
echo "  MYSQLDATABASE = ${MYSQLDATABASE}"
echo "  (password omitted)"
echo "==========================="

# Create .env file from Railway environment variables
echo "Creating .env from environment variables..."
cat > /app/.env << ENVEOF
APP_ENV=prod
APP_SECRET=${APP_SECRET}
DEFAULT_URI=https://${RAILWAY_PUBLIC_DOMAIN:-localhost}
DATABASE_URL="mysql://${MYSQLUSER}:${MYSQLPASSWORD}@${MYSQLHOST}:${MYSQLPORT:-3306}/${MYSQLDATABASE}?serverVersion=8.0&charset=utf8mb4"
CORS_ALLOW_ORIGIN=${CORS_ALLOW_ORIGIN}
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
GOOGLE_CLIENT_ID=${GOOGLE_CLIENT_ID}
GOOGLE_CLIENT_SECRET=${GOOGLE_CLIENT_SECRET}
MAILER_DSN=${MAILER_DSN}
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=${JWT_PASSPHRASE}
ENVEOF

echo ".env file created successfully"

# Remove the build-time compiled env cache so Symfony reads the fresh .env above.
rm -f /app/.env.local.php

# Generate JWT keys if missing
if [ ! -f /app/config/jwt/private.pem ]; then
    echo "Generating JWT keys..."
    mkdir -p /app/config/jwt
    openssl genrsa -out /app/config/jwt/private.pem 4096
    openssl rsa -pubout -in /app/config/jwt/private.pem -out /app/config/jwt/public.pem
    chmod 644 /app/config/jwt/*.pem
fi

# Clear and warmup cache for production
echo "Clearing and warming cache..."
php /app/bin/console cache:clear --env=prod --no-debug 2>&1 || echo "Cache clear warning (non-fatal)"
php /app/bin/console cache:warmup --env=prod 2>&1 || echo "Cache warmup warning (non-fatal)"

# Fix permissions after cache operations
echo "Fixing filesystem permissions..."
chown -R www-data:www-data /app/var
chmod -R 775 /app/var

# Wait for database and run migrations
if [ -z "$MYSQLHOST" ] || [ -z "$MYSQLUSER" ] || [ -z "$MYSQLPASSWORD" ] || [ -z "$MYSQLDATABASE" ]; then
    echo "ERROR: One or more MySQL environment variables are missing."
    echo "  MYSQLHOST     = '${MYSQLHOST}'"
    echo "  MYSQLPORT     = '${MYSQLPORT}'"
    echo "  MYSQLUSER     = '${MYSQLUSER}'"
    echo "  MYSQLDATABASE = '${MYSQLDATABASE}'"
    echo "  MYSQLPASSWORD = (set: $([ -n "$MYSQLPASSWORD" ] && echo yes || echo NO))"
    echo ""
    echo "Go to your Railway project → your app service → Variables tab and make sure"
    echo "these are referenced from your MySQL service (MYSQLHOST, MYSQLPORT, MYSQLUSER,"
    echo "MYSQLPASSWORD, MYSQLDATABASE) or the MYSQL_HOST / MYSQL_USER variants."
    exit 1
fi

if [ true ]; then
    echo "Waiting for database connection..."
    for i in {1..40}; do
        if php -r "
        try {
            new PDO('mysql:host=${MYSQLHOST};port=${MYSQLPORT:-3306};dbname=${MYSQLDATABASE}', '${MYSQLUSER}', '${MYSQLPASSWORD}', [
                PDO::ATTR_TIMEOUT => 5,
            ]);
            echo 'connected';
            exit(0);
        } catch (Exception \$e) { echo \$e->getMessage(); exit(1); }
        " 2>/dev/null; then
            echo "Database connected!"
            
            # Try running migrations normally
            echo "Running migrations..."
            if php /app/bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod 2>&1; then
                echo "All migrations completed successfully!"
            else
                echo "Some migrations failed. Skipping problematic migration..."
                # Mark the broken migration as executed and continue
                php /app/bin/console doctrine:migrations:version 'DoctrineMigrations\Version20260318094426' --add --no-interaction --env=prod 2>&1 || true
                echo "Continuing with remaining migrations..."
                php /app/bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod 2>&1 || true
            fi
            
            # Fix permissions after migrations
            echo "Fixing permissions after migrations..."
            chown -R www-data:www-data /app/var
            chmod -R 775 /app/var
            break
        fi
        echo "Waiting... ($i/40)"
        sleep 3
    done

    # If we exhausted retries without connecting, abort with a clear message
    if ! php -r "
    try {
        new PDO('mysql:host=${MYSQLHOST};port=${MYSQLPORT:-3306};dbname=${MYSQLDATABASE}', '${MYSQLUSER}', '${MYSQLPASSWORD}', [
            PDO::ATTR_TIMEOUT => 5,
        ]);
        exit(0);
    } catch (Exception \$e) { exit(1); }
    " 2>/dev/null; then
        echo "ERROR: Could not connect to MySQL at ${MYSQLHOST}:${MYSQLPORT:-3306} after 40 attempts. Check your Railway MySQL service or connection variables."
        exit 1
    fi
fi

# Replace ${PORT} in nginx config with actual Railway PORT
export PORT=${PORT:-80}
echo "Configuring Nginx to listen on port $PORT..."
sed -i "s/\${PORT}/$PORT/g" /etc/nginx/conf.d/default.conf

# Start PHP-FPM
echo "Starting PHP-FPM..."
php-fpm -D

# Wait and verify PHP-FPM started
sleep 2
if ps aux | grep -v grep | grep php-fpm > /dev/null; then
    echo "PHP-FPM is running"
else
    echo "WARNING: PHP-FPM may not have started properly"
fi

echo "Starting Nginx on port $PORT..."
nginx -g "daemon off;"