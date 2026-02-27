# iStore

Catalogo y pedidos por WhatsApp (Laravel).

## Requisitos
- PHP 8.2+
- MariaDB/MySQL
- Extensiones PHP: pdo_mysql, mbstring, xml, curl, zip, gd, fileinfo

## Configuracion
1. Copiar .env y ajustar:
- APP_URL
- DB_* (host, base, usuario, password)
- FILESYSTEM_DISK=public

2. Instalar dependencias y migrar (con consola):
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan optimize
```

## Hosting compartido (sin consola)
- Subir vendor ya compilado.
- Importar SQL en la base (migraciones + data).
- Asegurar permisos de escritura:
  - storage/
  - bootstrap/cache/

## Deploy en cPanel (paso a paso)
1. Subir todo el contenido de `src/src` a la carpeta del subdominio.
2. Asegurar que el `document root` apunte a `public/`. Si no se puede:
- Copiar contenido de `public/` a la raiz del subdominio.
- Ajustar `index.php` para que apunte a `vendor/` y `bootstrap/` en la raiz.
- Copiar `.htaccess` desde `public/` a la raiz.
3. Subir `vendor/` generado localmente (PHP 8.2+).
4. Importar dump SQL en phpMyAdmin.
5. Ajustar `.env`:
- APP_URL
- DB_HOST/DB_DATABASE/DB_USERNAME/DB_PASSWORD
- APP_KEY valido
6. Borrar cache vieja:
- eliminar `bootstrap/cache/config.php` si existe
7. Verificar permisos:
- `storage/`
- `bootstrap/cache/`

## Plantilla de importacion
Ruta de descarga:
- /admin/productos/template

Archivo fuente:
- resources/templates/import-template.csv

## Imagenes publicas
Se sirven por ruta Laravel (no depende de storage:link):
- /media/{path}

## Notas DB (compatibilidad MariaDB 11.4)
Workaround aplicado en hosting:
- charset: utf8
- collation: utf8_unicode_ci
- PDO::ATTR_EMULATE_PREPARES = true
- SET NAMES utf8 COLLATE utf8_unicode_ci

Revisar si el proveedor actualiza cliente PDO/MySQL para remover workaround.
