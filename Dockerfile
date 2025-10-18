FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    curl \
    openssl \
    postgresql-client \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    ctype \
    json

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev

RUN apk add --no-cache nodejs npm && npm install && npm run build

RUN apk add --no-cache nginx supervisor

RUN mkdir -p /run/nginx && \
    echo 'server { \
    listen 80; \
    root /app/public; \
    index index.php; \
    location ~ \.php$ { \
    fastcgi_pass 127.0.0.1:9000; \
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \
    include fastcgi_params; \
    } \
    location / { \
    try_files $uri $uri/ /index.php?$query_string; \
    } \
    }' > /etc/nginx/conf.d/default.conf

RUN echo '[supervisord] \
    nodaemon=true \
    [program:php-fpm] \
    command=/usr/local/sbin/php-fpm \
    autostart=true \
    autorestart=true \
    [program:nginx] \
    command=/usr/sbin/nginx -g "daemon off;" \
    autostart=true \
    autorestart=true' > /etc/supervisor/conf.d/app.conf

EXPOSE 80

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
