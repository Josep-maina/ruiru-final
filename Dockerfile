# Use the official PHP image with Apache
FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy all files to Apache's web root
COPY . /var/www/html/

# Set correct permissions (optional but recommended)
RUN chown -R www-data:www-data /var/www/html

# Set AllowOverride All to enable .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/i' /etc/apache2/apache2.conf

# Expose port 80 (optional for Docker local use)
EXPOSE 80
