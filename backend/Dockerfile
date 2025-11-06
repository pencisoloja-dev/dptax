FROM php:8.1-cli

# Solo instalar herramientas adicionales necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

# Copiar composer.json primero y verificar que se copió
COPY composer.json ./
RUN ls -la composer.json && cat composer.json

# Instalar dependencias de Composer con verbose para ver el error
RUN composer install --no-interaction --no-dev --optimize-autoloader -vvv

# Copiar el resto de los archivos de la aplicación
COPY api.php ./
COPY index.php ./

# Exponer puerto
ENV PORT=8080
EXPOSE 8080

# Iniciar servidor PHP integrado
CMD ["php", "-S", "0.0.0.0:$PORT", "api.php"]
