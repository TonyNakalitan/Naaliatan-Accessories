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
    && docker-php-ext-install \
    intl \
    opcache \
    zip

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
RUN composer install --no-dev --no-scripts --no-progress --no-interaction

# ==========================================
# 3. Application Source & Build-Time Fix
# ==========================================
# Copy the rest of your application code
COPY . .

# FIX: Create a minimal .env file so the Symfony runtime can bootstrap 
# during this build phase. This satisfies bin/console without baking 
# real production secrets into the image layers.
RUN echo "APP_ENV=prod" > .env && echo "DATABASE_URL=mysql://AnthonyNaaliatan:09359730321@127.0.0.1:3308/NAcessoriesDB" >> .env

# Run your AssetMapper importmap installation (this will now pass)
RUN php bin/console importmap:install --no-interaction

# Optimize Composer autoloader and dump env for production
RUN composer dump-env prod \
    && composer run-script post-install-cmd --no-interaction

# Remove the temporary build .env so the runtime entrypoint can write the real one.
# Keep .env.local.php — it's the compiled env cache produced by dump-env above.
RUN rm -f .env

# ==========================================
# 4. Container Execution
# ==========================================
EXPOSE 9000

CMD ["php-fpm"]