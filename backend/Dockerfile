FROM php:8.1-cli-alpine

# Instalar dependencias del OS (git, curl)
# Y AÑADIR extensiones de PHP (json es vital para composer)
RUN apk add --no-cache \
    openssl \
    ca-certificates \
    git \
    curl \
    php81-json \
    php81-mbstring \
    php81-curl \
    php81-openssl \
    php81-dom \
    php81-xml \
    php81-zip

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copiar composer.json primero
COPY composer.json ./

# Instalar dependencias de Composer (PHPMailer)
# Esto ahora funcionará porque tiene las extensiones
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Copiar el resto de los archivos de la aplicación
COPY api.php ./
COPY index.php ./

# Exponer puerto (EasyPanel usará $PORT, 8080 es el fallback)
# Corregido el formato de ENV por la advertencia (warning)
ENV PORT=8080
EXPOSE 8080

# Iniciar servidor ejecutando api.php directamente (más estable)
CMD ["php", "-S", "0.0.0.0:$PORT", "api.php"]
