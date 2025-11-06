FROM php:8.1-cli-alpine

# Instalar extensiones PHP directamente desde los repositorios de Alpine
RUN apk add --no-cache \
    # Extensiones PHP necesarias
    php81-zip \
    php81-dom \
    php81-xml \
    php81-mbstring \
    php81-openssl \
    php81-curl \
    php81-phar \
    php81-tokenizer \
    # Dependencias del sistema
    git \
    curl \
    zip \
    unzip \
    libzip-dev

# Crear enlaces simbólicos para que php pueda encontrar las extensiones
RUN ln -s /usr/bin/php81 /usr/bin/php

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copiar composer.json primero
COPY composer.json ./

# Instalar dependencias de Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Copiar el resto de los archivos de la aplicación
COPY api.php ./
COPY index.php ./

# Exponer puerto
ENV PORT=8080
EXPOSE 8080

# Iniciar servidor ejecutando api.php directamente
CMD ["php", "-S", "0.0.0.0:$PORT", "api.php"]
