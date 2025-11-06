# --- Etapa 1: Builder (Constructor) ---
FROM composer:2 as builder

WORKDIR /app

# Copiar composer.json y composer.lock (si existe)
COPY composer.json ./

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# --- Etapa 2: Aplicación Final ---
FROM php:8.1-cli-alpine

# Instalar dependencias del sistema necesarias para PHPMailer
RUN apk add --no-cache \
    openssl \
    ca-certificates

# Crear directorio de trabajo
WORKDIR /app

# Copiar vendor desde el builder
COPY --from=builder /app/vendor ./vendor

# Copiar archivos PHP
COPY api.php ./
COPY index.php ./

# Exponer puerto
EXPOSE 8080

# Iniciar servidor PHP en el puerto 8080
CMD ["php", "-S", "0.0.0.0:8080", "index.php"]
