# Usar una imagen oficial de Nginx (un servidor web ligero)
FROM nginx:1.25-alpine

# Copiar los archivos de tu sitio (index.html y la imagen) 
# desde la carpeta actual (frontend) a la carpeta del servidor web
COPY . /usr/share/nginx/html

# Exponer el puerto 80 (el puerto HTTP estándar)
EXPOSE 80

# Comando para iniciar el servidor Nginx
CMD ["nginx", "-g", "daemon off;"]
