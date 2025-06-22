# ---- Etapa 1: Builder ----
# Usamos una imagen de Node para construir la aplicación Angular
FROM node:20-alpine AS builder

# Establecemos el directorio de trabajo dentro del contenedor
WORKDIR /app

# Copiamos los archivos de dependencias y las instalamos
COPY package*.json ./
RUN npm install

# Copiamos el resto del código fuente de la aplicación
COPY . .

# Construimos la aplicación para producción. La salida estará en /app/dist/
RUN npm run build -- --configuration production

# ---- Etapa 2: Runner ----
# Usamos una imagen ligera de Nginx para servir los archivos compilados
FROM nginx:1.21-alpine

# Copiamos nuestra configuración personalizada de Nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copiamos los archivos estáticos construidos desde la etapa 'builder'
# La ruta de salida por defecto en Angular es dist/<project-name>/browser
COPY --from=builder /app/dist/ecommerce-test-leidy/browser /usr/share/nginx/html

# Exponemos el puerto 80, que es donde Nginx escucha por defecto
EXPOSE 80
