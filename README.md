# Backend Laravel - Sales Module API

API RESTful desarrollada en Laravel para la gestión integral de **productos**, **inventario**, **ventas** y **cálculo de impuestos**, con autenticación **JWT** y arquitectura por capas de negocio (`Services` & `Entities`).

## Características

- Autenticación JWT: registro, login, perfil actual (`who`), logout y refresh.
- Gestión de productos: listado paginado, creación, actualización y eliminación lógica.
- Inventario: cada producto tiene `stock`; al crear una venta se valida disponibilidad y se descuenta dentro de una transacción.
- Ventas: creación de ventas con conceptos y cálculo automático del total.
- Impuestos: cálculo de IVA por producto mediante `ProductService` y la entidad `Taxes`.
- Arquitectura por capas: controllers, form requests, services y entities.
- Pruebas automatizadas con Pest.

## Requisitos

- PHP 8.2 o superior
- Composer
- SQLite (recomendado para arranque rápido) o MariaDB/MySQL
- Node.js y npm (opcional, solo para assets frontend)

Extensiones PHP recomendadas: `pdo_sqlite`, `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`.

## Instalación local

### Opción A: SQLite (recomendada)

```bash
git clone <repositorio>
cd backendlaravel-code
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

La API quedará disponible en:

```txt
http://127.0.0.1:8000
```

### Opción B: MariaDB/MySQL

1. Crea la base de datos:

```sql
CREATE DATABASE backendlaravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Edita `.env`:

```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=backendlaravel
DB_USERNAME=root
DB_PASSWORD=
```

3. Ejecuta:

```bash
composer install
cp .env.example .env
# Ajusta la configuración de base de datos en .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

## Desarrollo

```bash
composer run dev
```

Esto inicia el servidor PHP, el worker de colas y Vite de forma concurrente.

Para compilar assets manualmente:

```bash
npm install
npm run build
```

## Variables de entorno relevantes

- `APP_KEY`: clave de la aplicación.
- `JWT_SECRET`: secreto para firmar tokens JWT.
- `DB_CONNECTION`, `DB_DATABASE`, etc.: configuración de base de datos.
- `KEY_ENCRYPT`: clave usada por el servicio de encriptación si se activa.
- `MAIL_*`: configuración de correo.

`JWT_SECRET` se genera con `php artisan jwt:secret`.

## Endpoints principales

Base: `/api`

### Públicos

- `POST /api/register` - Registrar usuario.
- `POST /api/login` - Iniciar sesión y obtener token JWT.

### Protegidos con JWT

Enviar en cabecera:

```txt
Authorization: Bearer <token>
```

Productos:

- `GET /api/product` - Listar productos paginados (`per_page`, `page`).
- `POST /api/product` - Crear producto.
- `GET /api/product/{product}` - Mostrar producto.
- `PUT|PATCH /api/product/{product}` - Actualizar producto.
- `DELETE /api/product/{product}` - Eliminar producto (soft delete).

Ventas:

- `GET /api/sale` - Listar ventas.
- `POST /api/sale` - Crear venta.

Impuestos:

- `GET /api/info/tax/{id}` - Calcular precio con IVA de un producto.

Sesión:

- `GET /api/who` - Usuario autenticado.
- `POST /api/logout` - Cerrar sesión.
- `POST /api/refresh` - Refrescar token.

## Arquitectura de negocio

- `app/Http/Controllers`: capa HTTP y respuestas JSON.
- `app/Http/Requests`: validación con form requests.
- `app/Business/Services`: lógica de negocio (`ProductService`, `CreateSaleService`).
- `app/Business/Entities`: entidades y reglas de cálculo (`ConceptEntity`, `SaleEntity`, `Taxes`).
- `app/Models`: persistencia Eloquent y relaciones.

## Inventario

- Los productos incluyen `stock`.
- Al crear una venta, el servicio verifica que cada producto tenga stock suficiente.
- Si alguna línea no cumple, la operación se revierte y se devuelve `422`.
- Si hay stock suficiente, se descuenta por cada concepto dentro de una transacción.

## Pruebas

Ejecutar toda la suite:

```bash
vendor/bin/pest
```

En entornos sin MariaDB:

```bash
DB_CONNECTION=sqlite DB_DATABASE=:memory: vendor/bin/pest
```

## Comandos útiles

```bash
php artisan migrate:fresh --seed
php artisan route:list
php artisan optimize:clear
vendor/bin/pint --format agent
```

## Seguridad y portabilidad

- `.env` no debe subirse al repositorio.
- Las bases de datos SQLite locales se ignoran.
- La caché generada de Laravel se ignora.
- Usa `.env.example` como plantilla y genera tus propias claves localmente.
- No compartas `APP_KEY`, `JWT_SECRET` ni credenciales de base de datos.
PHP
echo done