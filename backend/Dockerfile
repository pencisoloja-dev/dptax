FROM php:8.1-apache

# Solo instalar herramientas adicionales necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Copiar PRIMERO todos los archivos necesarios para composer
COPY composer.json ./
COPY composer.lock* ./

# Instalar dependencias de Composer
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Copiar el resto de los archivos de la aplicación
COPY api.php ./
COPY index.php ./

# Configurar Apache para el puerto 8080
RUN echo "Listen 8080" >> /etc/apache2/ports.conf
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

EXPOSE 8080

CMD ["apache2-foreground"]
