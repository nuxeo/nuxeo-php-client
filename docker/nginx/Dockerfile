FROM centos:6.6

# Set the locale
ENV LANG C
ENV LANGUAGE C
ENV LC_ALL C

RUN yum install -y epel-release

RUN getent group users > /dev/null || groupadd -g 100 -r users
RUN useradd -M -d /var/www/html -u 1000 -s /sbin/nologin php

RUN yum install -y nginx && chown -R php:users /var/lib/nginx

VOLUME ["/var/cache/nginx"]
EXPOSE 80 443

CMD ["nginx"]
