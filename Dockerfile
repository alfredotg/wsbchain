FROM php:cli
RUN apt update && \
  apt-get install libssl-dev
RUN yes | pecl install swoole && \
  echo "extension=swoole.so" > /usr/local/etc/php/conf.d/swoole.ini
EXPOSE 80
WORKDIR /app
COPY ./wait-for-it.sh ./wait-for-it.sh
COPY ./vendor ./vendor
COPY ./cmd ./cmd
COPY ./cmd.php ./cmd.php
COPY ./public ./public
COPY ./app ./app
CMD ["php"]

