# Usar una imagen base oficial de PHP 7.4 (CLI)
# Usamos 7.4 porque tu composer.json lo especifica
FROM php:7.4-cli-alpine

# Instalar dependencias del sistema operativo (git y para extensiones de PHP)
# Añadimos openssl y ca-certificates
RUN apk add --no-cache git openssl ca-certificates

# Instalar extensiones de PHP que PHPMailer puede necesitar (sockets)
# docker-php-ext-install es la herramienta oficial para esto
RUN docker-php-ext-install sockets

# Instalar Composer (el gestor de paquetes de PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copiar solo el composer.json para instalar dependencias
COPY composer.json ./

# Instalar las dependencias de PHP (PHPMailer)
# --no-dev: omite paquetes de desarrollo
# --optimize-autoloader: optimiza para producción
RUN composer install --no-dev --optimize-autoloader

# Copiar el resto del código de tu aplicación (api.php, index.php)
COPY . .

# Corregir la sintaxis de ENV (esto era el "warning")
ENV PORT=8080
EXPOSE 8080

# Este es el comando para iniciar tu API, tomado de tu composer.json
# Inicia el servidor web incorporado de PHP
CMD [ "php", "-S", "0.0.0.0:$PORT", "index.php" ]
