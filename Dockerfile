FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    curl \
    openssl \
    postgresql-client \
    oniguruma \
    libzip \
    icu-libs \
    && apk add --no-cache --virtual .build-deps \
    postgresql-dev \
    oniguruma-dev \
    icu-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    intl \
    zip \
    && apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .
RUN rm -rf vendor bootstrap/cache/*

# Create a basic .env file for the application to use inside container
RUN cp .env.example .env 2>/dev/null || (echo "APP_NAME=\"Support Ticket\"" > .env && \
    echo "APP_ENV=local" >> .env && \
    echo "APP_KEY=base64:CHANGEME_GENERATE_ME" >> .env && \
    echo "APP_DEBUG=true" >> .env && \
    echo "APP_URL=http://localhost" >> .env && \
    echo "DB_CONNECTION=pgsql" >> .env && \
    echo "DB_HOST=postgres" >> .env && \
    echo "DB_PORT=5432" >> .env && \
    echo "DB_DATABASE=support_ticket" >> .env && \
    echo "DB_USERNAME=postgres" >> .env && \
    echo "DB_PASSWORD=password" >> .env)

# Make .env writable for the entrypoint script to update it
RUN chmod 666 .env

RUN composer install --no-dev --ignore-platform-req=php

RUN apk add --no-cache nodejs npm && npm install && npm run build

RUN apk add --no-cache nginx supervisor


RUN mkdir -p /run/nginx && \
    printf '%s\n' \
    'user nginx;' \
    'worker_processes auto;' \
    'error_log /var/log/nginx/error.log warn;' \
    'pid /var/run/nginx.pid;' \
    'events {' \
    '    worker_connections 1024;' \
    '}' \
    'http {' \
    '    include /etc/nginx/mime.types;' \
    '    default_type application/octet-stream;' \
    '    log_format main '"'"'$remote_addr - $remote_user [$time_local] "$request"'"'"' \' \
    '                    '"'"'$status $body_bytes_sent "$http_referer"'"'"' \' \
    '                    '"'"'"$http_user_agent" "$http_x_forwarded_for"'"'"';' \
    '    access_log /var/log/nginx/access.log main;' \
    '    sendfile on;' \
    '    tcp_nopush on;' \
    '    keepalive_timeout 65;' \
    '    gzip on;' \
    '    server {' \
    '        listen 80;' \
    '        root /app/public;' \
    '        index index.php;' \
    '        location ~ \.php$ {' \
    '            fastcgi_pass 127.0.0.1:9000;' \
    '            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;' \
    '            include fastcgi_params;' \
    '        }' \
    '        location / {' \
    '            try_files $uri $uri/ /index.php?$query_string;' \
    '        }' \
    '    }' \
    '}' \
    > /etc/nginx/nginx.conf

RUN mkdir -p /etc/supervisor/conf.d && \
    echo '[supervisord]' > /etc/supervisor/supervisord.conf && \
    echo 'nodaemon=true' >> /etc/supervisor/supervisord.conf && \
    echo 'include_files=/etc/supervisor/conf.d/*.conf' >> /etc/supervisor/supervisord.conf

RUN mkdir -p /etc/supervisor/conf.d && \
    echo '[program:php-fpm]' > /etc/supervisor/conf.d/app.conf && \
    echo 'command=/usr/local/sbin/php-fpm' >> /etc/supervisor/conf.d/app.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/app.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/app.conf && \
    echo '' >> /etc/supervisor/conf.d/app.conf && \
    echo '[program:nginx]' >> /etc/supervisor/conf.d/app.conf && \
    echo 'command=/usr/sbin/nginx -g "daemon off;"' >> /etc/supervisor/conf.d/app.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/app.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/app.conf

EXPOSE 80

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
