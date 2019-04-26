#FROM php:7.3.4-apache
#
#MAINTAINER Giulio Troccoli-Allard
#
#COPY . /srv/app
#COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf
#
#WORKDIR /srv/app
#
#RUN docker-php-ext-install mbstring pdo pdo_mysql \
#&& chown -R www-data:www-data /srv/app
FROM php:7.3.4-apache

# Install system dependencies
# - git: required so Composer can clone packages from source, and by the deploy task to clone the ansible repo
# - libpng-dev: required by the PHP gd extension
# - zlib (provided by zlib1g-dev): required by the PHP zip extension
# libssl-dev: required so that ansible can be installed with pip
# libffi-dev: required so that ansible can be installed with pip
# openssh-client: required so we can use SSH
# python-dev: needed so that ansible can be installed with pip
# python-setuptools: required so we can install pip (which we use to install ansible)
# vpnc: required for rank deployments
RUN apt-get update && apt-get install -y git libpng-dev zlib1g-dev libzip-dev libssl-dev libffi-dev openssh-client \
 python-dev python-setuptools zip unzip gnupg2 vpnc libxml2-dev

# Install Google Chrome needed libraries
RUN apt-get install -yq gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 \
 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 \
 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 \
 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 \
 libnss3 lsb-release xdg-utils wget

# Install Chromium
RUN apt-get install -y chromium

# Enable rewrite mod for Apache
RUN a2enmod rewrite

# Install PHP extensions
# - gd: required by the intervention image library
# - zip: required so Composer can use packages from dist
# - pdo_mysql: required by PDO to connect to a MySQL database
RUN docker-php-ext-install gd zip pdo_mysql calendar bcmath soap

# Install NodeJS 8.x
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash - && apt-get install -y nodejs

# Install Composer
RUN php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php && \
    php composer-setup.php && php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

# Install Yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && \
    apt-get update && apt-get install yarn

COPY .docker/config/composer.json /root/.composer/
#COPY .docker/config/composer.lock /root/.composer/

RUN cd /root/.composer/ && php /usr/local/bin/composer install --no-interaction --no-progress && \
    php /usr/local/bin/composer clearcache

# this workaround is to let docker write logs. we could think to remove logs from the directory
RUN usermod -u 1000 www-data

RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN mkdir -p /var/www/vhosts/app

COPY .docker/config/apache/vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY .docker/config/php/50-custom.ini /usr/local/etc/php/conf.d/

RUN rm -rf /tmp/*

WORKDIR /var/www/vhosts/app
