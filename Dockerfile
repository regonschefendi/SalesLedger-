FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql pgsql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --optimize-autoloader --no-dev

# Install Node.js versi 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install package NPM dan Build Vite untuk Production
RUN npm install \
    && npm run build

RUN chmod -R 775 storage bootstrap/cache

EXPOSE ${PORT:-8080}

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}