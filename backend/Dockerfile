FROM php:8.1-cli-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    openssl \
    ca-certificates \
    git \
    unzip \
    zip \
    libzip-dev

# Instalar extensión zip de PHP
RUN docker-php-ext-install zip

# Instalar Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar SOLO los archivos necesarios
COPY composer.json ./
COPY api.php ./
COPY index.php ./

# Limpiar cache de composer y ejecutar con máximo detalle
RUN composer clear-cache && \
    composer install --no-dev --optimize-autoloader --ignore-platform-reqs -vvv

# Exponer puerto
EXPOSE 8080

# Iniciar servidor
CMD ["php", "-S", "0.0.0.0:8080", "index.php"]
