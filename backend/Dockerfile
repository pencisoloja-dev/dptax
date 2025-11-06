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
ENV PORT=8080
EXPOSE 8080

# Iniciar servidor PHP integrado
CMD ["php", "-S", "0.0.0.0:$PORT", "api.php"]
