FROM centos:6.6

# Set the locale
ENV LANG C
ENV LANGUAGE C
ENV LC_ALL C

RUN yum install -y epel-release

RUN yum install -y php-fpm php-mysql php-pdo php-gd php-mbstring php-mcrypt php-pecl-geoip php-xml php-pecl-xdebug php-soap

RUN getent group users > /dev/null || groupadd -g 100 -r users
RUN useradd -M -d /var/www/html -u 1000 -s /sbin/nologin php

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
