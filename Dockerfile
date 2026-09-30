FROM php:8.2-alpine

# Set direktori kerja di dalam container
WORKDIR /var/www/html

# Install dependensi sistem dan ekstensi PHP yang dibutuhkan Laravel (pdo_sqlite, zip)
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    libzip-dev \
    sqlite-dev \
    && docker-php-ext-install pdo_sqlite zip

# Salin binary Composer resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# =========================================================================
# STRATEGI LAYER CACHING:
# 1. Salin HANYA file dependensi Composer terlebih dahulu
# =========================================================================
COPY composer.json composer.lock ./

# 2. Pasang dependensi PHP tanpa menjalankan script/autoloader aplikasi
# Layer ini akan di-CACHE oleh Docker selama composer.json dan composer.lock tidak berubah
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist

# =========================================================================
# 3. Salin seluruh sisa kode aplikasi setelah dependensi terpasang
# =========================================================================
COPY . .

# 4. Generate autoloader teroptimasi setelah kode aplikasi disalin
RUN composer dump-autoload --optimize

# 5. Salin .env.example menjadi .env jika belum ada dan generate APP_KEY
RUN if [ ! -f .env ]; then cp .env.example .env && php artisan key:generate; fi

# 6. Pastikan file database SQLite dan folder storage memiliki izin akses yang tepat
RUN touch database/database.sqlite \
    && chmod 777 database/database.sqlite \
    && chmod 777 database \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ekspos port 8000 agar dapat diakses dari host
EXPOSE 8000

# Perintah default untuk menjalankan server bawaan Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
