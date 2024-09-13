FROM php:8.1-fpm

WORKDIR /var/www

RUN rm -rf /var/www/html

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    apt-utils \
    libonig-dev \
    supervisor \
    && docker-php-ext-install gd \
    && docker-php-ext-install pcntl \
    && rm -rf /var/lib/apt/lists/*

# Instala o Redis via PECL
RUN pecl install redis \
    && docker-php-ext-enable redis

RUN docker-php-ext-install zip pdo_mysql mbstring exif
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install gd

# Cria o usuário e grupo antes de usar chown
RUN groupadd -g 1000 laravel && \
    useradd -u 1000 -ms /bin/bash -g laravel user

# Cria os diretórios e ajusta permissões
RUN mkdir -p /var/log/supervisor /var/run /var/www/storage /var/www/bootstrap/cache && \
    chown -R user:laravel /var/log/supervisor /var/run /var/www/storage /var/www/bootstrap/cache && \
    touch /var/log/supervisor/supervisord.log && \
    chmod 777 /var/log/supervisor/supervisord.log

# Ajusta permissões para o diretório do projeto
RUN chown -R user:laravel /var/www

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copia o código e ajusta permissões
COPY --chown=user:laravel . /var/www

# Copia os arquivos de configuração do Supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisor.conf
COPY horizon.conf /etc/supervisor/conf.d/horizon.conf

USER user

EXPOSE 9001

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]



