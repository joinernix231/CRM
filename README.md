# Mini CRM

API Laravel para gestión de clientes y contactos.

## Requisitos

- Docker Desktop
- Git

## Inicio rápido con Docker

Desde la carpeta del proyecto (`mini-crm`):

```bash
docker compose up --build
```

Servicios:

| Servicio    | URL                      |
|-------------|--------------------------|
| API Laravel | http://localhost:8000  |
| phpMyAdmin  | http://localhost:8080  |
| MySQL       | `localhost:3307`       |

Credenciales MySQL (por defecto):

- Base de datos: `mini_crm`
- Usuario: `crm_user`
- Contraseña: `crm_password`
- Root: `root` / `root`

### Primera ejecución

El contenedor `app` hace automáticamente:

1. Copia `.env.example` → `.env` si no existe
2. Genera `APP_KEY`
3. `composer install`
4. Migraciones de base de datos
5. Servidor en el puerto 8000

### Comandos útiles

```bash
# En segundo plano
docker compose up -d --build

# Ver logs
docker compose logs -f app

# Artisan dentro del contenedor
docker compose exec app php artisan migrate:fresh --seed

# Detener
docker compose down
```

### Variables de entorno

Copia `.env.example` a `.env` para desarrollo local sin Docker. Para Docker, los valores de base de datos ya están en `.env.example` (`DB_HOST=db`).

El `docker-compose.yml` también inyecta las variables de BD en el servicio `app`.

## Endpoints principales

- `POST /api/login`
- `POST /api/register`
- `GET /api/clients` (requiere sesión/token)
- Telescope (debug): http://localhost:8000/telescope

## Nota sobre el compose anterior

Si antes usabas `docker compose` desde la carpeta padre (`Prueba Tecnica`), ahora debes ejecutarlo desde **`mini-crm`**, donde vive la configuración del CRM.
