# ---------- BUILD STAGE (Node untuk Vite) ----------
FROM node:22-alpine AS build

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build


# ---------- APP STAGE (Laravel) ----------
FROM php:8.3-cli-alpine

# install dependencies
RUN apk add --no-cache \
    bash \
    git \
    curl \
    zip \
    unzip \
    autoconf \
    g++ \
    make \
    linux-headers \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev


RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

RUN pecl install redis \
    && docker-php-ext-enable redis

# install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# copy semua file project
COPY . .

# copy hasil build Vite
COPY --from=build /app/public/build public/build

# install dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# set permission
RUN chmod -R 775 storage bootstrap/cache

# expose port Laravel
EXPOSE 8000

# jalankan Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
