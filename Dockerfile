# ==========================================
# 1. Base Image Setup
# ==========================================
FROM php:8.2-fpm-alpine

# Install system dependencies & PHP extensions required by Symfony
RUN apk add --no-cache \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    nginx \
    bash \
    openssl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    intl \
    opcache \
    zip \
    pdo_mysql \
    gd \
    exif \
    fileinfo

# Configure PHP upload limits
RUN echo "upload_max_filesize=15M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=15M" >> /usr/local/etc/php/conf.d/uploads.ini

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory inside the container
WORKDIR /app

# ==========================================
# 2. Dependency Management
# ==========================================
# Copy only composer files first to leverage Docker cache layers
COPY composer.json composer.lock ./

# Install dependencies without running Symfony flex/scripts yet
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --no-scripts --no-progress --no-interaction \
    && php -r " \
        \$f = 'vendor/knpuniversity/oauth2-client-bundle/src/Client/OAuth2Client.php'; \
        \$c = file_get_contents(\$f); \
        \$old = 'return \$request->query->has(\$key) ? \$request->query->get(\$key) : \$request->request->get(\$key);'; \
        \$new = 'return \$request->query->has(\$key) ? \$request->query->get(\$key) : (\$request->request->has(\$key) ? \$request->request->get(\$key) : null);'; \
        file_put_contents(\$f, str_replace(\$old, \$new, \$c)); \
        echo \"Patched OAuth2Client.php\n\"; \
    "

# ==========================================
# 3. Application Source & Build-Time Fix
# ==========================================
# Copy the rest of your application code
COPY . .

# FIX: Create a minimal .env file so the Symfony runtime can bootstrap 
# during this build phase. This satisfies bin/console without baking 
# real production secrets into the image layers.
RUN echo "APP_ENV=prod" > .env && echo "DATABASE_URL=mysql://user:password@localhost:3306/db" >> .env

# Optimize Composer autoloader for production
RUN composer dump-autoload --optimize --no-dev

# Pre-create upload directories so they exist in the image
RUN mkdir -p /app/public/images/profiles \
    && mkdir -p /app/public/images/products \
    && mkdir -p /app/public/images/characters \
    && chown -R www-data:www-data /app/public/images \
    && chmod -R 775 /app/public/images

# Remove the temporary build .env so the runtime entrypoint can write the real one.
RUN rm -f .env

# Copy nginx configuration
COPY nginx-main.conf /etc/nginx/nginx.conf
COPY nginx.conf /etc/nginx/conf.d/default.conf

# ==========================================
# 4. Container Execution
# ==========================================
EXPOSE 80

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

CMD ["/entrypoint.sh"]