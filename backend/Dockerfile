FROM php:8.1-cli

WORKDIR /app

# Copiar todos los archivos de la aplicación
COPY . .

# Crear la estructura de vendor manualmente
RUN mkdir -p vendor/composer

# Crear un autoloader básico
RUN echo '<?php function __autoload($class) { include str_replace("\\\\", "/", $class) . ".php"; } spl_autoload_register("__autoload"); ?>' > vendor/autoload.php

# Crear composer.json simulado para PHPMailer y Monolog
RUN echo '{}' > composer.json

# Exponer puerto
EXPOSE 8080

# CAMBIO AQUÍ: Puerto fijo en lugar de variable
CMD ["php", "-S", "0.0.0.0:8080", "api.php"]
