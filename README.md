# Mini CRM

Monolito **Laravel 9** + **Vue 3** (Composition API, Pinia) para gestión de clientes y contactos. Prueba técnica Full Stack - dominio agencia/consultoría.

## Stack

| Capa | Tecnología |
|------|------------|
| Backend | Laravel 9.x, PHP 8.2, MySQL 8 |
| Auth | Laravel Sanctum (Bearer token) |
| Frontend | Vue 3 (`<script setup>`), Pinia, Vite |
| Arquitectura | Rutas web en Laravel; Vue montado en Blade (sin Vue Router ni Inertia) |
| Validación API | Form Requests |
| Persistencia | Repositorio (`prettus/l5-repository`) + criterios de consulta |
| Opcionales | Docker, PHPUnit, exportación PDF (DomPDF), Laravel Telescope (debug local) |

## Funcionalidades

- **Autenticación:** login, logout y registro vía API (`POST /api/register`). La UI usa el usuario de prueba creado por `UserSeeder`.
- **Clientes:** CRUD, búsqueda por texto y filtro por estado (`active`, `inactive`, `prospect`). Paginación en API.
- **Contactos:** CRUD anidado por cliente desde el detalle del cliente.
- **Reglas de negocio:**
  - Un contacto pertenece a un solo cliente; un cliente tiene muchos contactos.
  - Solo un contacto **primario** por cliente (al marcar otro como primario, el anterior se desmarca).
  - Cada cliente/contacto queda asociado al `user_id` del usuario autenticado (aislamiento multi-usuario).
- **PDF:** en el detalle del cliente, botón *Descargar PDF* con datos del cliente y tabla de contactos.

---

## Inicio rápido (recomendado: Docker)

Desde la **raíz del repositorio**:

```bash
docker compose up -d --build
docker compose exec app php artisan migrate:fresh --seed
npm install
npm run build
```

Abre http://localhost:8000/login e inicia sesión con el [usuario de prueba](#usuario-de-prueba-userseeder) (tabla siguiente).

| Servicio | URL |
|----------|-----|
| Aplicación | http://localhost:8000 |
| Login | http://localhost:8000/login |
| Clientes | http://localhost:8000/clients |
| phpMyAdmin | http://localhost:8080 |
| MySQL (host) | `localhost:3307` |
| Telescope (local) | http://localhost:8000/telescope |

El contenedor `app`, en el primer arranque, copia `.env`, genera `APP_KEY`, instala dependencias PHP y ejecuta migraciones. Los **datos demo** (usuario, clientes, contactos) los carga el comando `migrate:fresh --seed` de arriba.

### Usuario de prueba (`UserSeeder`)

El seeder `database/seeders/UserSeeder.php` deja un usuario listo para revisar la app sin registrarse manualmente:

| Campo | Valor |
|-------|--------|
| Nombre | `Joiner Davila` |
| Email | `Joiner@email.com` |
| Contraseña | `secret` |

Orden de ejecución de seeders (`DatabaseSeeder`):

1. **UserSeeder** - crea o actualiza el usuario demo (`updateOrCreate` por email).
2. **ClientSeeder** - 10 clientes de ejemplo para ese usuario.
3. **ContactSeeder** - contactos por cliente (uno marcado como primario).

Para repetir datos desde cero:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Sin Docker:

```bash
php artisan migrate:fresh --seed
```

Los teléfonos de clientes y contactos usan formato móvil Colombia: `3` + 9 dígitos (`3#########`), generados en las factories.

### Frontend (necesario para ver la UI)

Los assets compilados **no** están en Git (`public/build` en `.gitignore`):

```bash
npm install
npm run build
```

Desarrollo con recarga: `npm run dev` (Vite en http://localhost:5173, Laravel en http://localhost:8000).

Opcional en `.env`:

```env
VITE_API_URL=http://localhost:8000/api
```

### MySQL (Docker)

| Variable | Valor por defecto |
|----------|-------------------|
| Base de datos | `mini_crm` |
| Usuario | `crm_user` |
| Contraseña | `crm_password` |
| Root | `root` / `root` |

---

## Requisitos previos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (recomendado)
- Git
- Node.js 18+ y npm (compilar frontend en el host)

## Desarrollo local sin Docker

1. `cp .env.example .env` - ajusta `DB_HOST=127.0.0.1` si usas MySQL local.
2. `composer install`
3. `php artisan key:generate`
4. `php artisan migrate:fresh --seed`
5. `php artisan serve`
6. `npm install && npm run build`

## Tests

```bash
docker compose exec app php artisan test
```

Incluye auth, CRUD, aislamiento por usuario, contacto primario único y descarga PDF.

---

## API (resumen)

Rutas protegidas:

```http
Authorization: Bearer {token}
Accept: application/json
```

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/login` | Login |
| POST | `/api/register` | Registro (solo API; ver decisiones técnicas) |
| POST | `/api/logout` | Cerrar sesión |
| GET | `/api/user` | Perfil |
| GET/POST | `/api/clients` | Listar / crear |
| GET/PUT/DELETE | `/api/clients/{id}` | Ver / editar / borrar |
| GET | `/api/clients/{id}/pdf` | PDF del cliente |
| GET/POST | `/api/clients/{client}/contacts` | Contactos |
| GET/PUT/DELETE | `/api/clients/{client}/contacts/{id}` | Contacto |

Query en listado de clientes: `search`, `status`, `page`, `per_page`.

## Rutas web (UI)

| Ruta | Pantalla |
|------|----------|
| `/login` | Login |
| `/dashboard` | Panel |
| `/clients` | Listado |
| `/clients/create` | Alta cliente |
| `/clients/{id}` | Detalle, contactos, PDF |

---

## Decisiones técnicas relevantes

Esta sección responde al enunciado: *documentar decisiones que consideres relevantes*. Para cada punto: **qué problema había**, **qué elegí** y **por qué**.

### 1. Monolito Laravel + Vue en Blade (sin SPA)

| | |
|---|---|
| **Contexto** | El enunciado exige monolito, Vite y prohibe Vue Router, Inertia y frontend separado. |
| **Decisión** | Varias entradas Vite (`app.js`, `clients.js`, `client.js`, `dashboard.js`) montan componentes Vue en vistas Blade; Laravel define todas las URLs en `routes/web.php`. |
| **Por qué** | Cumple la arquitectura pedida, deploy simple y evita duplicar rutas entre Laravel y el front. Pinia comparte estado dentro de cada pantalla. |

### 2. API JSON + Sanctum (Bearer)

| | |
|---|---|
| **Contexto** | Autenticación con Sanctum y rutas del CRM protegidas. |
| **Decisión** | Login devuelve `access_token`; el front lo guarda y lo envía en `Authorization: Bearer`. Middleware `VerifyApiToken` valida el token y deja `user_id` en sesión para el resto del request. |
| **Por qué** | Separación clara API/UI, compatible con pruebas HTTP y con un front parcial sin SPA completa. |

### 3. Validación en Form Requests

| | |
|---|---|
| **Contexto** | El enunciado pide validaciones con Form Requests. |
| **Decisión** | Un request por acción (`CreateClientAPIRequest`, `ShowClientAPIRequest`, etc.) y clase base `APIRequest` para respuestas de error uniformes (`success`, `message`, `data` por campo). |
| **Por qué** | Controladores delgados, reglas reutilizables (p. ej. `clientIdRules`) y mensajes de validación consistentes para el front (`ValidationAlert`, `FormField`). |

### 4. Aislamiento por `user_id` (multi-usuario)

| | |
|---|---|
| **Contexto** | Cada registro asociado al usuario que lo creó y solo usuarios autenticados. |
| **Decisión** | Columna `user_id` en `clients` y `contacts`; listados filtran por `session('user_id')`; regla `ObjectBelongsToModelRule` impide acceder a IDs de otro usuario (respuesta 400). |
| **Por qué** | Implementa la regla de negocio sin depender solo del front; los tests verifican que un usuario no ve clientes ajenos. |

### 5. Un solo contacto primario

| | |
|---|---|
| **Contexto** | Solo puede existir un contacto primario por cliente. |
| **Decisión** | Al crear o actualizar con `is_primary = true`, `ContactRepository` desmarca los demás del mismo `client_id`; el modelo refuerza la restricción en eventos. |
| **Por qué** | La regla vive en el dominio (no solo en UI); tests unitarios y de API cubren el caso. |

### 6. Repositorio + criterios de consulta

| | |
|---|---|
| **Contexto** | Listados con búsqueda, filtro por estado, relaciones y paginación. |
| **Decisión** | `prettus/l5-repository` con criterios (`WhereFieldCriteria`, `FiltersCriteria`, `WithRelationshipsCriteria`, etc.). |
| **Por qué** | Evita repetir scopes en cada método del controlador y facilita combinar filtros (`search` + `status`) desde query string. |

### 7. Registro en API; demo con `UserSeeder`

| | |
|---|---|
| **Contexto** | Se pide registro, login y logout; la revisión debe ser rápida. |
| **Decisión** | `POST /api/register` implementado; pantalla de login usa el usuario del seeder (`Joiner@email.com` / `secret`). |
| **Por qué** | Cumple el requisito de registro sin duplicar flujos en Vue; el evaluador entra en un paso con datos ya cargados. |

### 8. PDF con DomPDF (mejora opcional)

| | |
|---|---|
| **Contexto** | Mejora opcional: exportar PDF. |
| **Decisión** | `GET /api/clients/{id}/pdf`, plantilla Blade `resources/views/pdf/client-detail.blade.php`, descarga desde el detalle del cliente. |
| **Por qué** | Reutiliza stack Laravel (Blade + paquete maduro), misma autorización que el resto de la API. |

### 9. Assets front fuera de Git

| | |
|---|---|
| **Contexto** | Entregar repo clonable. |
| **Decisión** | `public/build` en `.gitignore`; el README exige `npm run build` tras clonar. |
| **Por qué** | Evita binarios enormes en Git; es el flujo estándar con Vite en Laravel. |

### 10. Docker + tests automatizados (mejoras opcionales)

| | |
|---|---|
| **Decisión** | `docker-compose` (app, MySQL, phpMyAdmin); PHPUnit contra MySQL en Docker; Telescope opcional en local. |
| **Por qué** | Quien revisa levanta el entorno sin instalar PHP/MySQL en el host si no lo desea. |

---

## Supuestos de negocio y alcance

- Cada usuario ve **solo** sus clientes y contactos (CRM por consultor/vendedor).
- Estados de cliente: `active`, `inactive`, `prospect`.
- Teléfonos de ejemplo en formato Colombia (`3` + 9 dígitos).
- Paginación en API; la UI puede solicitar `per_page` alto para mostrar más filas en demo.
- Telescope solo para desarrollo local, no requerido para usar el CRM.

---

## Estructura del proyecto

```
app/Http/Controllers/Api/
app/Http/Requests/Api/
app/Repositories/
database/seeders/          # UserSeeder, ClientSeeder, ContactSeeder
resources/js/              # Vue 3 + Pinia
resources/views/pdf/
tests/Feature/Api/
docker-compose.yml
```

## Comandos útiles

```bash
docker compose logs -f app
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan test
docker compose down
```

## Notas

- No versionar `.env`; usar `.env.example`.
- Carpeta local `mini-crm/` (si existe) es un resto de una estructura antigua; el proyecto está en la raíz del repo y esa carpeta está en `.gitignore`.

---

Prueba técnica - Desarrollador Full Stack (Laravel + Vue).
