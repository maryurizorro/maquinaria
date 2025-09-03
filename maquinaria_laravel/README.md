# Sistema de Gestión de Maquinaria - API Laravel

Sistema completo de gestión de maquinaria con autenticación JWT/Sanctum, CRUD completo y consultas especiales.

## Características

- ✅ **Autenticación con Laravel Sanctum**
- ✅ **CRUD completo para todas las entidades**
- ✅ **Validaciones robustas**
- ✅ **Consultas especiales y reportes**
- ✅ **Dashboard con estadísticas**
- ✅ **Documentación completa**
- ✅ **Datos de prueba incluidos**

## Requisitos

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL/SQLite
- Laravel 12.x

## Instalación

### 1. Clonar el proyecto
```bash
git clone <url-del-repositorio>
cd maquinaria_laravel
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar variables de entorno
```bash
cp .env.example .env
```

Editar el archivo `.env` con las credenciales de tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=maquinaria_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### 4. Generar clave de aplicación
```bash
php artisan key:generate
```

### 5. Ejecutar migraciones y seeders
```bash
php artisan migrate:fresh --seed
```

### 6. Iniciar el servidor
```bash
php artisan serve
```

El servidor estará disponible en: `http://localhost:8000`

## Estructura de la Base de Datos

### Tablas Principales
- **users** - Usuarios del sistema (admin, empleado, supervisor)
- **empresas** - Empresas cliente
- **representantes** - Representantes legales de las empresas
- **categoria_maquinarias** - Categorías de maquinaria
- **tipo_maquinarias** - Tipos específicos de maquinaria
- **mantenimientos** - Servicios de mantenimiento disponibles
- **solicitudes** - Solicitudes de mantenimiento
- **detalle_solicituds** - Detalles de cada solicitud
- **empleados** - Empleados del sistema
- **solicitud_empleados** - Asignación de empleados a solicitudes

## Endpoints de la API

### Autenticación
- `POST /api/register` - Registro de usuarios
- `POST /api/login` - Inicio de sesión
- `POST /api/logout` - Cerrar sesión
- `GET /api/me` - Obtener usuario actual

### CRUD Principal
- **Empresas**: `/api/empresas`
- **Representantes**: `/api/representantes`
- **Categorías**: `/api/categorias`
- **Tipos de Maquinaria**: `/api/tipos-maquinaria`
- **Mantenimientos**: `/api/mantenimientos`
- **Solicitudes**: `/api/solicitudes`
- **Detalles de Solicitud**: `/api/detalle-solicitudes`
- **Empleados**: `/api/empleados`
- **Asignaciones**: `/api/solicitud-empleados`

### Consultas Especiales
- `/api/consultas/empleados-ordenados`
- `/api/consultas/maquinaria-pesada-costosa`
- `/api/consultas/empresa-mas-solicitudes`
- `/api/consultas/maquinas-argos`
- `/api/consultas/solicitudes-empleado`
- `/api/consultas/representantes-sin-solicitudes`
- `/api/consultas/listado-solicitudes`
- `/api/consultas/solicitud-por-codigo`
- `/api/consultas/mantenimientos-retroexcavadoras`
- `/api/consultas/solicitudes-octubre-2023`

### Dashboard
- `/api/dashboard/stats` - Estadísticas generales
- `/api/dashboard/solicitudes-por-estado` - Solicitudes por estado
- `/api/dashboard/top-empresas` - Top empresas

## Datos de Prueba

El sistema incluye datos de prueba automáticamente:

### Usuarios
- **Admin**: `admin@example.com` / `password`
- **Empleado 1**: `juan@example.com` / `password`
- **Empleado 2**: `maria@example.com` / `password`

### Empresas
- Argos S.A.
- Constructora ABC
- Ingeniería XYZ

### Categorías y Tipos
- **Maquinaria Pesada**: Retroexcavadora, Excavadora, Bulldozer
- **Maquinaria Ligera**: Vibrador de Concreto

### Mantenimientos
- MANT-001: Mantenimiento Preventivo Retroexcavadora ($1,500,000)
- MANT-002: Reparación Sistema Hidráulico ($2,500,000)
- MANT-003: Cambio de Aceite y Filtros ($800,000)
- MANT-004: Calibración Vibrador ($300,000)

## Pruebas con Postman

### 1. Configuración Inicial
1. Crear nueva colección "API Maquinaria"
2. Configurar variables de entorno:
   - `base_url`: `http://localhost:8000/api`
   - `token`: (se llenará después del login)
3. Configurar headers globales:
   - `Content-Type`: `application/json`
   - `Accept`: `application/json`

### 2. Flujo de Prueba
1. **Login**: `POST {{base_url}}/login`
   ```json
   {
       "email": "admin@example.com",
       "password": "password"
   }
   ```
2. **Copiar token** de la respuesta
3. **Configurar Authorization**: `Bearer {{token}}`
4. **Probar endpoints CRUD** en orden lógico
5. **Probar consultas especiales**
6. **Probar dashboard**

## Documentación Completa

Ver el archivo `API_DOCUMENTATION.md` para documentación detallada de todos los endpoints.

## Estructura del Proyecto

```
maquinaria_laravel/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── EmpresaController.php
│   │   ├── RepresentanteController.php
│   │   ├── CategoriaMaquinariaController.php
│   │   ├── TipoMaquinariaController.php
│   │   ├── MantenimientoController.php
│   │   ├── SolicitudController.php
│   │   ├── DetalleSolicitudController.php
│   │   ├── EmpleadoController.php
│   │   ├── SolicitudEmpleadoController.php
│   │   └── ConsultaController.php
│   └── Models/
│       ├── User.php
│       ├── Empresa.php
│       ├── Representante.php
│       ├── CategoriaMaquinaria.php
│       ├── TipoMaquinaria.php
│       ├── Mantenimiento.php
│       ├── Solicitud.php
│       ├── DetalleSolicitud.php
│       ├── Empleado.php
│       └── SolicitudEmpleado.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
├── config/
│   └── cors.php
├── API_DOCUMENTATION.md
└── README.md
```

## Comandos Útiles

```bash
# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Ver rutas disponibles
php artisan route:list

# Crear nuevo controlador
php artisan make:controller NombreController

# Crear nueva migración
php artisan make:migration create_nombre_table
```

## Seguridad

- ✅ Autenticación con Laravel Sanctum
- ✅ Validación de datos en todos los endpoints
- ✅ Protección CSRF
- ✅ Configuración CORS
- ✅ Middleware de autenticación en rutas protegidas

## Soporte

Para soporte técnico o preguntas sobre el sistema, contactar al desarrollador.

## Licencia

Este proyecto está bajo la Licencia MIT.
