FROM php:8.1-cli-alpine

# Actualizar los repositorios e instalar herramientas de compilación
RUN apk update && apk add --no-cache \
    # Herramientas de compilación esenciales
    autoconf g++ make \
    # Dependencias para extensiones PHP
    openssl-dev \
    curl-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    zlib-dev \
    # Utilidades
    git \
    curl \
    zip \
    unzip

# Configurar e instalar extensiones PHP paso a paso
RUN docker-php-ext-configure zip && \
    docker-php-ext-install zip

RUN docker-php-ext-install dom

RUN docker-php-ext-install xml

RUN docker-php-ext-install mbstring

RUN docker-php-ext-install openssl

RUN docker-php-ext-install curl

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
