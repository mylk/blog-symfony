FROM ubuntu:18.04

ENV DEBIAN_FRONTEND noninteractive

RUN apt update \
    && apt install -y locales curl zip unzip git mariadb-client php7.2-fpm php7.2-cli \
    php7.2-mysql php7.2-mbstring php7.2-xml php7.2-curl php7.2-gd \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && mkdir /run/php \
    && apt clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && locale-gen en_US.UTF-8

ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

ADD build/php/php-fpm.conf /etc/php/7.2/fpm/php-fpm.conf
ADD build/php/www.conf /etc/php/7.2/fpm/pool.d/www.conf
ADD build/php/bootstrap.sh /application/build/php/bootstrap.sh
ADD build/php/php_fpm.ini /etc/php/7.2/fpm/php.ini
ADD build/php/php_cli.ini /etc/php/7.2/cli/php.ini

EXPOSE 9000

CMD "/application/build/php/bootstrap.sh"
