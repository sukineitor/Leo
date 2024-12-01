# Usa una imagen base con PHP preinstalado
FROM php:8.1-apache

# Instala extensiones necesarias para tu proyecto
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia los archivos de tu proyecto al contenedor
COPY . /var/www/html/

# Configura el directorio de trabajo
WORKDIR /var/www/html/

# Expon el puerto 8080 para Render
EXPOSE 8080

# Comando para iniciar el servidor
CMD ["php", "-S", "0.0.0.0:8080", "-t", "."]
