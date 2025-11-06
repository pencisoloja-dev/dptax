# --- Etapa 1: "Builder" (Constructor) ---
# Usar la imagen oficial de Composer, que tiene todo lo necesario
FROM composer:2.0 as builder

WORKDIR /app

# Copiar solo el composer.json
COPY composer.json ./

# Instalar las dependencias (PHPMailer)
# Esto creará una carpeta /app/vendor
RUN composer install --no-dev --optimize-autoloader

# --- Etapa 2: "Final" (Aplicación) ---
# Empezar desde una imagen limpia de PHP
FROM php:7.4-cli-alpine

# Instalar solo las dependencias de *runtime* que PHPMailer necesita
RUN apk add --no-cache \
    openssl \
    ca-certificates

# Instalar la extensión 'sockets'
RUN docker-php-ext-install sockets

WORKDIR /app

# Copiar la carpeta 'vendor' (con PHPMailer ya instalado) desde el 'builder'
COPY --from=builder /app/vendor ./vendor

# Copiar el resto de tu código PHP
COPY api.php ./
COPY index.php ./

# Corregir la sintaxis de ENV
ENV PORT=8080
EXPOSE 8080

# Comando para iniciar tu API
CMD [ "php", "-S", "0.0.0.0:$PORT", "index.php" ]
