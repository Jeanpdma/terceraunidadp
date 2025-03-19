FROM php:8.2-fpm-alpine

# Variables de entorno
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/var/www/.composer

# Instala dependencias básicas
RUN apk update && apk add --no-cache \
    mariadb-client \
    zip \
    unzip \
    git \
    curl \
    nano \
    autoconf \
    g++ \
    make \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    freetype-dev \
    libpng-dev \
    jpeg-dev \
    zlib-dev \
    openssl \
    openssl-dev

# Habilita extensiones PHP
RUN docker-php-ext-install pdo_mysql session fileinfo tokenizer dom zip mbstring gd

# Limpia herramientas innecesarias
RUN apk del gcc g++ make libc-dev && rm -rf /var/cache/apk/*

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia los archivos del proyecto
COPY . .

# Instala dependencias de Composer
RUN composer install --no-interaction --no-plugins --no-scripts --optimize-autoloader

# Instala dependencias de Node.js y construye assets
RUN npm install
RUN npm run build

# Configura permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/bootstrap/cache

# Expone el puerto 8000
EXPOSE 8000

# Comando para ejecutar el servidor de Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
