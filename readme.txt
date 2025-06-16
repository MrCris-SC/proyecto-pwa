# Proyecto PWA - Instrucciones de Despliegue

## Requisitos previos
- PHP >= 8.1
- Composer
- Node.js y npm
- MySQL

## Pasos para desplegar después de clonar

1. **Clona el repositorio**
   ```
   git clone 
   cd proyecto-pwa
   ```

2. **Instala las dependencias de PHP**
   ```
   composer install
   ```

3. **Instala las dependencias de Node.js**
   ```
   npm install
   ```

4. **Copia el archivo de entorno y configura tus variables**
   ```
   cp .env.example .env
   ```
   Edita el archivo `.env` y configura la conexión a la base de datos y otras variables necesarias.

5. **Genera la clave de la aplicación**
   ```
   php artisan key:generate
   ```

6. **Ejecuta las migraciones**
   ```
   php artisan migrate
   ```

7. **Compila los assets de frontend**
   ```
   npm run dev
   ```
   (Para producción, usa `npm run build`)

8. **Inicia el servidor de desarrollo**
   ```
   php artisan serve
   ```

---

Al ejecutar las migracones para las migraciones para la base de datos
puedes iniciar sesión con el usuario por default:
email: admin@admin.com
pass: admin123