FROM php:8.0-cli

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN apt-get update -y && apt-get install -y curl unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer --quiet

RUN install-php-extensions xdebug

COPY ./ "$PHP_INI_DIR/conf.d"

ARG HOST_UID=1000
ARG HOST_GID=1000
ARG HOST_USER=docker-user
ARG HOST_GROUP=docker-group

RUN echo '%sudonopswd ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers \
    && groupadd -g $HOST_GID $HOST_GROUP \
    && groupadd sudonopswd \
    && useradd -m -l -g $HOST_GROUP -u $HOST_UID $HOST_USER \
    && usermod -aG sudo $HOST_USER \
    && usermod -aG sudonopswd $HOST_USER \
    && chown -R $HOST_USER:$HOST_GROUP /opt \
    && chmod 755 /opt

USER $HOST_USER

WORKDIR /app
