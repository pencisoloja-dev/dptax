FROM php:8.1-cli-alpine

# Instalar composer y dependencias
RUN apk add --no-cache \
    composer \
    openssl \
    ca-certificates \
    git

WORKDIR /app

# Copiar archivos
COPY . ./

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Exponer puerto
EXPOSE 8080

# Iniciar servidor
CMD ["php", "-S", "0.0.0.0:8080", "index.php"]
