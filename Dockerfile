FROM php:8.2-apache

RUN apt-get update -qq && \
    apt-get install -y -qq \
        libpng-dev libjpeg-dev libwebp-dev libzip-dev \
        libicu-dev libxml2-dev libonig-dev \
        unzip && \
    docker-php-ext-configure gd --with-jpeg --with-webp && \
    docker-php-ext-install -j4 gd pdo pdo_mysql zip intl xml opcache mbstring && \
    a2enmod rewrite && \
    echo 'ServerName localhost' >> /etc/apache2/apache2.conf && \
    printf '<Directory /var/www/html>\n  AllowOverride All\n  Require all granted\n</Directory>\n' \
        > /etc/apache2/conf-enabled/override.conf && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Install ConcreteCMS 9.5.1 from local zip (pre-downloaded to docker/ccms.zip)
COPY docker/ccms.zip /tmp/ccms.zip
RUN cd /tmp && unzip -q ccms.zip && \
    cp -a /tmp/concrete-cms-9.5.1/. /var/www/html/ && \
    rm -rf /tmp/concrete-cms-9.5.1 /tmp/ccms.zip && \
    mkdir -p /var/www/html/application/files \
             /var/www/html/application/config \
             /var/www/html/packages && \
    chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    chmod -R 775 /var/www/html/application/files \
                 /var/www/html/application/config \
                 /var/www/html/packages
