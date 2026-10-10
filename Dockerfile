# =========================================================================
# STAGE 1: BUILDER (Tahap Membangun - Mengompilasi & Menginstal Dependensi)
# Menggunakan versi spesifik php:8.2.27-alpine3.20 dan composer:2.8.5
# =========================================================================
FROM php:8.2.27-alpine3.20 AS builder

WORKDIR /var/www/html

# Install build dependencies dan header pengembangan untuk kompilasi ekstensi
RUN apk add --no-cache \
    git \
    curl \
    unzip \
    libzip-dev \
    sqlite-dev \
    && docker-php-ext-install pdo_sqlite zip

# Salin binary Composer resmi dengan versi terkunci (2.8.5)
COPY --from=composer:2.8.5 /usr/bin/composer /usr/bin/composer

# Salin berkas composer untuk memanfaatkan layer caching
COPY composer.json composer.lock ./

# Instal pustaka PHP tanpa dev dependencies
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist

# Salin seluruh sisa kode aplikasi
COPY . .

# Generate autoloader teroptimasi untuk mode produksi
RUN composer dump-autoload --optimize --no-dev

# Siapkan berkas environment .env dan database SQLite jika belum ada
RUN if [ ! -f .env ]; then cp .env.example .env && php artisan key:generate; fi \
    && touch database/database.sqlite \
    && chmod 777 database/database.sqlite \
    && chmod 777 database

# =========================================================================
# STAGE 2: RUNNER (Tahap Menjalankan - Runtime Minimal & Aman)
# Hanya memuat dependensi runtime esensial, tanpa compiler dan tanpa Composer
# =========================================================================
FROM php:8.2.27-alpine3.20 AS runner

WORKDIR /var/www/html

# Hanya instal pustaka runtime shared libraries (tanpa git, compiler, atau *-dev headers)
RUN apk add --no-cache \
    libzip \
    sqlite-libs \
    curl \
    && rm -rf /var/cache/apk/*

# Salin ekstensi PHP yang sudah dikompilasi dari stage builder
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

# Salin aplikasi Laravel yang sudah siap beserta folder vendor dari stage builder
COPY --from=builder --chown=www-data:www-data /var/www/html /var/www/html

# Beri izin akses ke direktori writable (storage, cache, database) untuk user www-data
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Ekspos port 8000
EXPOSE 8000

# Pengamanan: Jalankan aplikasi menggunakan user non-root (www-data)
USER www-data

# Pemantauan kesehatan container secara otomatis
HEALTHCHECK --interval=20s --timeout=5s --start-period=5s --retries=3 \
    CMD curl -f http://127.0.0.1:8000/api/tasks || exit 1

# Perintah menjalankan web server Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
