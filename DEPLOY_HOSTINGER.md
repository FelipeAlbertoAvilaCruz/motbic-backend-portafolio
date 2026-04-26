# Guía de Despliegue en Hostinger (API REST)

Esta guía está optimizada para desplegar tu API Laravel en un entorno de hosting compartido como Hostinger.

## 1. Preparación Local

Antes de subir los archivos, asegúrate de que tu proyecto esté limpio y listo.

1.  **Habilitar Rutas API**: Ya hemos configurado `bootstrap/app.php` para cargar `routes/api.php`.
2.  **Instalar dependencias de producción**:
    En tu terminal local, ejecuta:
    ```bash
    composer install --optimize-autoloader --no-dev
    ```
    *Esto elimina paquetes de desarrollo (como tests) y optimiza la carga de clases.*

3.  **Comprimir el proyecto**:
    Crea un archivo `.zip` con todo el contenido de la carpeta `backend`, **EXCEPTO**:
    - `.git/`
    - `.env` (crearás uno nuevo en el servidor)
    - `tests/` (opcional, no se necesitan en producción)

## 2. Subida de Archivos

1.  Entra al **Administrador de Archivos** de Hostinger.
2.  Navega a la carpeta `public_html`.
3.  Sube tu archivo `.zip` y descomprímelo.
    *   *Recomendación de seguridad*: Lo ideal es subir los archivos del proyecto a una carpeta paralela a `public_html` (ej. `motbic-api`) y solo poner el contenido de `public` dentro de `public_html`.
    *   *Método simple*: Si subes todo dentro de `public_html`, asegúrate de proteger los archivos sensibles.

## 3. Configuración del Entorno (.env)

1.  En el servidor, busca el archivo `.env.example` y renómbralo a `.env`.
2.  Edítalo con los siguientes valores críticos:

```ini
APP_NAME=MotbicAPI
APP_ENV=production
APP_KEY=... (Copia la clave de tu .env local o genera una nueva)
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Configuración de Base de Datos Hostinger
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

## 4. Configuración del Servidor Web (.htaccess)

Hostinger usa Apache/LiteSpeed. Necesitamos asegurar que las peticiones se dirijan correctamente a la carpeta `public` donde reside el `index.php` de Laravel.

Crea o edita el archivo `.htaccess` en la **raíz** de tu proyecto (al mismo nivel que `app`, `bootstrap`, etc.) con este contenido:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## 5. Base de Datos

Como es una API nueva:

1.  Ve a **phpMyAdmin** en Hostinger.
2.  Selecciona tu base de datos (`your_db_name`).
3.  Ve a la pestaña **Importar**.
4.  Sube el archivo `schema.sql` que está en la raíz de tu proyecto local (o expórtalo si hiciste cambios).
    *   *Nota*: Si tienes acceso SSH en Hostinger, es mejor ejecutar `php artisan migrate --force`.

## 6. Permisos

Asegúrate de que las siguientes carpetas tengan permisos de escritura (775 o 755):
- `storage/`
- `bootstrap/cache/`

## 7. Verificación

Tu API debería estar accesible ahora.
- Prueba un endpoint público: `GET https://tu-dominio.com/api/modelos`
- Si recibes un error 500, revisa el archivo `storage/logs/laravel.log`.

## 8. CORS (Importante para APIs)

Si vas a consumir esta API desde una app móvil o web en otro dominio, asegúrate de configurar `config/cors.php` (o publicar la configuración si no existe) para permitir los orígenes correctos.
