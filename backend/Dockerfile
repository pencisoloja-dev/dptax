FROM php:8.1-cli

WORKDIR /app

# Instalar herramientas necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Descargar e instalar PHPMailer manualmente
RUN curl -L https://github.com/PHPMailer/PHPMailer/archive/refs/tags/v6.9.1.tar.gz -o phpmailer.tar.gz \
    && tar -xzf phpmailer.tar.gz \
    && mkdir -p vendor/phpmailer/phpmailer \
    && cp -r PHPMailer-6.9.1/src/* vendor/phpmailer/phpmailer/ \
    && rm -rf phpmailer.tar.gz PHPMailer-6.9.1

# Copiar archivos de la aplicación
COPY api.php ./
COPY index.php ./

# Exponer puerto
EXPOSE 8080

# Usar puerto fijo
CMD ["php", "-S", "0.0.0.0:8080", "api.php"]
