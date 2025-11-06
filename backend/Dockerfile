FROM php:8.1-cli-alpine

# Instalar dependencias del OS incluyendo herramientas de compilación
RUN apk add --no-cache \
    # Herramientas de compilación necesarias
    autoconf g++ make \
    # Dependencias para extensiones PHP
    openssl-dev \
    curl-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    # Utilidades
    git \
    curl \
    zip \
    unzip

# Instalar las extensiones de PHP
RUN docker-php-ext-install zip dom xml mbstring openssl curl

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copiar composer.json primero
COPY composer.json ./

# Instalar dependencias de Composer (PHPMailer)
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Copiar el resto de los archivos de la aplicación
COPY api.php ./
COPY index.php ./

# Exponer puerto
ENV PORT=8080
EXPOSE 8080

# Iniciar servidor ejecutando api.php directamente
CMD ["php", "-S", "0.0.0.0:$PORT", "api.php"]
