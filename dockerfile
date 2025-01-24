# Use the official PHP image with Apache
FROM php:8.1-apache

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy application files into the container
COPY . /var/www/html/

# Set permissions for the application folder
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
