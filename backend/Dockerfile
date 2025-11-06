FROM php:8.1-cli-alpine

# Instalar dependencias
RUN apk add --no-cache \
    openssl \
    ca-certificates \
    git

WORKDIR /app

# Copiar archivos de la aplicación
COPY api.php ./
COPY index.php ./

# Descargar PHPMailer directamente desde GitHub
RUN mkdir -p vendor/phpmailer/phpmailer && \
    cd vendor/phpmailer/phpmailer && \
    wget -q https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.tar.gz && \
    tar -xzf v6.9.1.tar.gz --strip-components=1 && \
    rm v6.9.1.tar.gz

# Crear autoload manual
RUN echo '<?php spl_autoload_register(function($class) { $prefix = "PHPMailer\\\\PHPMailer\\\\"; $base_dir = __DIR__ . "/phpmailer/phpmailer/src/"; $len = strlen($prefix); if (strncmp($prefix, $class, $len) !== 0) return; $relative_class = substr($class, $len); $file = $base_dir . str_replace("\\\\", "/", $relative_class) . ".php"; if (file_exists($file)) require $file; });' > vendor/autoload.php

# Exponer puerto
EXPOSE 8080

# Iniciar servidor
CMD ["php", "-S", "0.0.0.0:8080", "index.php"]
