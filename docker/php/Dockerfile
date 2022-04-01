FROM yiisoftware/yii2-php:7.4-fpm

RUN apt-get update \
    && apt-get install -y \
        git \
        postgresql-client \
        wget \
        curl \
		zip \
		unzip \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

WORKDIR /app
