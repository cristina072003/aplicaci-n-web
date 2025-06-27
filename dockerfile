FROM php:8.0.0-apache
ARG DEBIAN_FRONTEND=noninteractive

RUN apt update && \
    apt install -y \
    libzip-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli zip
RUN a2enmod rewrite