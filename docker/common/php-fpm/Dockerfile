# Build the production-ready PHP-FPM image
FROM php:8.4-fpm AS production

# Install common system packages and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libssl-dev \
    && docker-php-ext-install -j"$(nproc)" \
    bcmath \
    intl \
    opcache \
    pdo_mysql \
    pdo_pgsql \
    zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js and npm for asset building
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Builds a dev-only layer on top of the production image
FROM production AS development

USER root

# Add ARGs for syncing permissions
ARG UID=1000
ARG GID=1000

# Create a new user with matching host UID/GID so shared volumes stay writable
RUN set -eux; \
    if getent group "${GID}" >/dev/null; then \
    group_name=$(getent group "${GID}" | cut -d: -f1); \
    else \
    group_name=www; \
    groupadd -g "${GID}" "${group_name}"; \
    fi; \
    if id -u www >/dev/null 2>&1; then \
    userdel -r www; \
    fi; \
    useradd -m -u "${UID}" -g "${GID}" -s /bin/bash www; \
    sed -i 's/user = www-data/user = www/g' /usr/local/etc/php-fpm.d/www.conf; \
    sed -i "s/group = www-data/group = ${group_name}/g" /usr/local/etc/php-fpm.d/www.conf

# Set the working directory
WORKDIR /var/www

# Copy the entrypoint script used for development bootstrapping
COPY ./docker/development/php-fpm/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Note: Running as root in development for Docker volume compatibility
# In production, use proper ownership and non-root user
# USER www

# Change the default command to run the entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
