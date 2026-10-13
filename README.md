# Backend Laravel - Sales Module API

![PHP 8.2+](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)
![Laravel 11](https://img.shields.io/badge/Laravel-11.x-FF2D20?logo=laravel)
![JWT Auth](https://img.shields.io/badge/Auth-JWT-000000)
![Pest](https://img.shields.io/badge/Tests-Pest-9b51e0)
![License: MIT](https://img.shields.io/badge/License-MIT-green)

API RESTful desarrollada en Laravel para la gestión integral de **productos**, **inventario**, **ventas** y **cálculo de impuestos**, con autenticación **JWT** y arquitectura por capas de negocio (`Services` & `Entities`).

## Tabla de contenido

- [Características](#características)
- [Requisitos](#requisitos)
- [Instalación local](#instalación-local)
- [Endpoints principales](#endpoints-principales)
- [Arquitectura de negocio](#arquitectura-de-negocio)
- [Inventario](#inventario)
- [Ejemplos de uso](#ejemplos-de-uso)
- [Pruebas](#pruebas)
- [Comandos útiles](#comandos-útiles)
- [Seguridad y portabilidad](#seguridad-y-portabilidad)

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

Protección:
- `public` = sin JWT
- `jwt` = requiere `Authorization: Bearer <token>`

| Método | Ruta | Protección | Descripción |
|---|---|---:|---|
| POST | `/api/register` | public | Registrar usuario |
| POST | `/api/login` | public | Iniciar sesión y obtener token JWT |
| GET | `/api/category` | jwt | Listar categorías |
| POST | `/api/category` | jwt | Crear categoría |
| GET | `/api/category/{category}` | jwt | Mostrar categoría |
| PUT/PATCH | `/api/category/{category}` | jwt | Actualizar categoría |
| DELETE | `/api/category/{category}` | jwt | Eliminar categoría (soft delete) |
| POST | `/api/category/{category}/restore` | jwt | Restaurar categoría eliminada |
| POST | `/api/product` | jwt | Crear producto |
| GET | `/api/product/{product}` | jwt | Mostrar producto |
| PUT/PATCH | `/api/product/{product}` | jwt | Actualizar producto |
| DELETE | `/api/product/{product}` | jwt | Eliminar producto (soft delete) |
| GET | `/api/sale` | jwt | Listar ventas |
| POST | `/api/sale` | jwt | Crear venta |
| GET | `/api/info/tax/{id}` | jwt | Calcular precio con IVA |
| GET | `/api/who` | jwt | Usuario autenticado |
| POST | `/api/logout` | jwt | Cerrar sesión |
| POST | `/api/refresh` | jwt | Refrescar token |

> Las categorías usan soft delete. Un producto conserva su `category_id` aunque la categoría esté eliminada; para reactivarla, usar `POST /api/category/{category}/restore`.

### Cabecera de autenticación

```txt
Authorization: Bearer <token>
```

## Arquitectura de negocio

Flujo general:

```txt
HTTP Request
  -> Controller
  -> Form Request / Validation
  -> Service (ProductService, CreateSaleService)
  -> Entity (ConceptEntity, SaleEntity, Taxes)
  -> Eloquent Model
  -> Database
  <- JSON Response
```

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

## Ejemplos de uso

### Login

Request:

```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

Response:

```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

### Crear venta

Request:

```http
POST /api/sale
Authorization: Bearer <token>
Content-Type: application/json

{
  "email": "cliente@example.com",
  "sale_date": "2025-03-12 10:30:00",
  "concepts": [
    {
      "quantity": 2,
      "product_id": 1
    },
    {
      "quantity": 1,
      "product_id": 2
    }
  ]
}
```

Response:

```json
{
  "id": 1,
  "email": "cliente@example.com",
  "sale_date": "2025-03-12 10:30:00",
  "concepts": [
    {
      "quantity": 2,
      "price": 100.0,
      "product_id": 1,
      "total": 200.0
    }
  ],
  "total": 200.0
}
```

El precio de cada concepto se toma del producto en el momento de la venta.

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
- Los directorios `storage/` y `bootstrap/cache/` contienen archivos generados por Laravel y no deben versionarse como código fuente.

## Licencia

Este proyecto se distribuye bajo la licencia MIT.