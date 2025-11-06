# Usar una imagen base oficial de PHP 7.4 (CLI)
# Usamos 7.4 porque tu composer.json lo especifica
FROM php:7.4-cli-alpine

# Instalar dependencias necesarias para Composer y Git
RUN apk add --no-cache git

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

# EasyPanel asignará un $PORT automáticamente. 
# Definimos uno por defecto por si acaso.
ENV PORT 8080
EXPOSE 8080

# Este es el comando para iniciar tu API, tomado de tu composer.json
# Inicia el servidor web incorporado de PHP
CMD [ "php", "-S", "0.0.0.0:$PORT", "index.php" ]