FROM php:8.1-cli-alpine

# Instalar dependencias
RUN apk add --no-cache \
    openssl \
    ca-certificates \
    git \
    curl

# Instalar Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copiar composer.json primero
COPY composer.json ./

# Instalar dependencias de Composer
# Esto creará el directorio vendor/ y el autoload.php correctamente
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Copiar el resto de los archivos de la aplicación
COPY api.php ./
COPY index.php ./

# Exponer puerto
EXPOSE 8080

# --- ESTA ES LA LÍNEA CORREGIDA ---
# Iniciar servidor ejecutando api.php directamente
CMD ["php", "-S", "0.0.0.0:8080", "api.php"]
