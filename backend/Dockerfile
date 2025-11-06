# Usar una imagen base oficial de PHP 7.4 (CLI)
FROM php:7.4-cli-alpine

# Instalar dependencias del sistema operativo
# Añadimos git, openssl, y las herramientas para compilar extensiones
# También las librerías para zip, mbstring y ctype
RUN apk add --no-cache \
    git \
    openssl \
    ca-certificates \
    build-base \
    libzip-dev \
    oniguruma-dev # Requerido para mbstring

# Instalar las extensiones de PHP que PHPMailer y Composer necesitan
RUN docker-php-ext-install \
    sockets \
    zip \
    mbstring \
    ctype

# Instalar Composer (el gestor de paquetes de PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copiar solo el composer.json para instalar dependencias
COPY composer.json ./

# Instalar las dependencias de PHP (PHPMailer)
# --ignore-platform-reqs es un "martillo" por si algo sigue fallando
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Limpiar las dependencias de compilación para reducir el tamaño
RUN apk del build-base oniguruma-dev

# Copiar el resto del código de tu aplicación (api.php, index.php)
COPY . .

# Corregir la sintaxis de ENV
ENV PORT=8080
EXPOSE 8080

# Comando para iniciar tu API
CMD [ "php", "-S", "0.0.0.0:$PORT", "index.php" ]
