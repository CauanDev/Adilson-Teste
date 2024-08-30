# Dockerfile

# Usa a imagem base do PHP com suporte a FPM e PHP 8.2
FROM php:8.2-fpm

# Instala dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install zip

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho no container
WORKDIR /var/www

# Copia os arquivos do projeto para o diretório de trabalho
COPY . .

# Instala as dependências do PHP
RUN composer install

# Instala as dependências do Node.js
RUN npm install

# Expõe as portas necessárias para o Laravel e o Vue.js
EXPOSE 8000 8080

# Define o comando para iniciar o servidor PHP e o frontend Vue.js
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 & npm run dev"]
