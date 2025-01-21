# Use the official PHP image with Apache
FROM php:8.2.12-apache

# Install necessary PHP extensions and set permissions
RUN docker-php-ext-install mysqli pdo pdo_mysql && \
    chown -R www-data:www-data /var/www/html

# Copy application files into the container
COPY . /var/www/html/

# Switch to non-root user
USER www-data

# Expose port 80
EXPOSE 80
