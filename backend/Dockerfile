FROM php:8.1-cli-alpine

# Instalar dependencias del OS (git, curl) y las librerías -dev
# necesarias para compilar las extensiones de PHP.
RUN apk add --no-cache \
    # Para la extensión openssl
    openssl-dev \
    # Para la extensión curl
    curl-dev \
    # Para dom y xml
    libxml2-dev \
    # Para la extensión zip
    libzip-dev \
    git \
    curl \
    zip \
    unzip

# Instalar las extensiones de PHP que PHPMailer y Composer necesitan
# 'json' ya está incluido en PHP 8.1 y no debe instalarse aquí
RUN docker-php-ext-install zip dom xml mbstring openssl curl

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copiar composer.json primero
COPY composer.json ./

# Instalar dependencias de Composer (PHPMailer)
# Esto AHORA funcionará porque las extensiones están instaladas
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Copiar el resto de los archivos de la aplicación
COPY api.php ./
COPY index.php ./

# Exponer puerto
ENV PORT=8080
EXPOSE 8080

# Iniciar servidor ejecutando api.php directamente
CMD ["php", "-S", "0.0.0.0:$PORT", "api.php"]
