FROM php:8.1-cli-bullseye

# Instalar extensiones PHP usando apt (no docker-php-ext-install)
RUN apt-get update && apt-get install -y \
    php8.1-zip \
    php8.1-dom \
    php8.1-xml \
    php8.1-mbstring \
    php8.1-curl \
    git \
    curl \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Instalar Composer globalmente (si no se instaló via apt)
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
