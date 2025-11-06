FROM php:8.1-cli-alpine

# Instalar dependencias del OS (git, curl, y librerías para extensiones)
# 'zip', 'unzip' y 'libzip-dev' son necesarios para composer y la extensión zip
# 'libxml2-dev' es para dom y xml
RUN apk add --no-cache \
    openssl \
    ca-certificates \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libxml2-dev

# Instalar las extensiones de PHP que PHPMailer y Composer necesitan
# Esta es la forma oficial de hacerlo en la imagen de PHP
RUN docker-php-ext-install zip dom xml mbstring openssl curl json

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

# Exponer puerto (EasyPanel usará $PORT, 8080 es el fallback)
ENV PORT=8080
EXPOSE 8080

# Iniciar servidor ejecutando api.php directamente
CMD ["php", "-S", "0.0.0.0:$PORT", "api.php"]
