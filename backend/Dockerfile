# Usar una imagen base oficial de PHP 7.4 (CLI)
FROM php:7.4-cli-alpine

# Instalar dependencias del sistema operativo
# Añadimos git, openssl, y las herramientas para compilar extensiones
RUN apk add --no-cache git openssl ca-certificates build-base

# Instalar las dependencias para la extensión ZIP
RUN apk add --no-cache libzip-dev

# Instalar las extensiones de PHP que PHPMailer necesita (sockets y zip)
RUN docker-php-ext-install sockets zip

# Instalar Composer (el gestor de paquetes de PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copiar solo el composer.json para instalar dependencias
COPY composer.json ./

# Instalar las dependencias de PHP (PHPMailer)
RUN composer install --no-dev --optimize-autoloader

# Limpiar las dependencias de compilación para reducir el tamaño
RUN apk del build-base

# Copiar el resto del código de tu aplicación (api.php, index.php)
COPY . .

# Corregir la sintaxis de ENV
ENV PORT=8080
EXPOSE 8080

# Comando para iniciar tu API
CMD [ "php", "-S", "0.0.0.0:$PORT", "index.php" ]
