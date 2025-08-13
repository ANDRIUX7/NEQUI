# Usa PHP con Apache
FROM php:8.2-apache

# Copia todos los archivos del proyecto al contenedor
COPY . /var/www/html/

# Da permisos (opcional, pero recomendado)
RUN chmod -R 755 /var/www/html

# Expone el puerto 80 (web)
EXPOSE 80
