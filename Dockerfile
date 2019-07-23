FROM socialsigninapp/docker-debian-gcp-php72:latest

ARG DEBIAN_FRONTEND=noninteractive

RUN rm /etc/apache2/sites-enabled/* && \
    a2enmod rewrite deflate 

RUN curl -so /usr/local/bin/composer https://getcomposer.org/composer.phar  && chmod 755 /usr/local/bin/composer && \
    echo GMT > /etc/timezone && dpkg-reconfigure --frontend noninteractive tzdata && \
    ln -sf /dev/stdout /var/log/apache2/access.log \
    && ln -sf /dev/stderr /var/log/apache2/error.log

COPY ./provisioning/apache-host /etc/apache2/sites-enabled/default.conf
COPY ./provisioning/php.ini /etc/php/7.2/apache2/conf.d/logging.ini
COPY . /srv/webhook-client

WORKDIR /srv/webhook-client

RUN /usr/local/bin/composer -n install && php setup.php

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
EXPOSE 80
