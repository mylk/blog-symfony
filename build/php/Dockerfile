FROM ubuntu:16.04

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update \
    && apt-get install -y locales curl zip unzip git python-software-properties \
       php7.0-fpm php7.0-cli php7.0-mysql php7.0-mbstring php7.0-xml php7.0-curl php7.0-gd \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer \
    && mkdir /run/php \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN locale-gen en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

ADD . /application

RUN cd /application \
    && rm -rf app/cache \
    && rm -rf app/logs \
    && rm -rf web/img/captcha \
    && mkdir app/cache \
    && mkdir app/logs \
    && mkdir web/img/captcha \
    && chmod -R 0777 app/cache \
    && chmod -R 0777 app/logs \
    && chmod -R 0777 web/img/captcha \
    && touch web/img/captcha/.gitkeep

ADD build/php/php-fpm.conf /etc/php/7.0/fpm/php-fpm.conf
ADD build/php/www.conf /etc/php/7.0/fpm/pool.d/www.conf

EXPOSE 9000

CMD "/application/build/php/bootstrap.sh"