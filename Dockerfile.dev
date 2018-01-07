FROM jakubsacha/symfony-docker:php7.1-dev

RUN apt-get update && apt-get install -y
RUN apt-get install -y zlib1g-dev libsasl2-dev libssl-dev
RUN docker-php-ext-install zip

RUN mkdir -p /usr/local/openssl/include/openssl/ && \
    ln -s /usr/include/openssl/evp.h /usr/local/openssl/include/openssl/evp.h && \
    mkdir -p /usr/local/openssl/lib/ && \
    ln -s /usr/lib/x86_64-linux-gnu/libssl.a /usr/local/openssl/lib/libssl.a && \
    ln -s /usr/lib/x86_64-linux-gnu/libssl.so /usr/local/openssl/lib/

RUN pecl install mongodb

RUN echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/20-mongodb.ini && \
	echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/20-mongodb.ini
