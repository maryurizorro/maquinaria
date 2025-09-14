![alt text](image.png)# Documentación API - Sistema de Maquinaria

## Información General
- **Base URL**: `http://localhost:8000/api`
- **Autenticación**: Laravel Sanctum (Bearer Token)
- **Formato de respuesta**: JSON

## Configuración Inicial

### 1. Configurar Base de Datos
```bash
# Configurar archivo .env con credenciales de base de datos
# Ejecutar migraciones y seeders
php artisan migrate:fresh --seed
```

### 2. Generar clave de aplicación
```bash
php artisan key:generate
```

## Endpoints de Autenticación

### Registro de Usuario
- **URL**: `POST /api/register`
- **Headers**: `Content-Type: application/json`
- **Body**:
```json
{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "rol": "empleado"
}
```

### Login
- **URL**: `POST /api/login`
- **Headers**: `Content-Type: application/json`
- **Body**:
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```

### Logout
- **URL**: `POST /api/logout`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {token}`

### Obtener Usuario Actual
- **URL**: `GET /api/me`
- **Headers**: `Authorization: Bearer {token}`

## Endpoints CRUD

### Empresas
- `GET /api/empresas` - Listar empresas
- `POST /api/empresas` - Crear empresa
- `GET /api/empresas/{id}` - Obtener empresa
- `PUT /api/empresas/{id}` - Actualizar empresa
- `DELETE /api/empresas/{id}` - Eliminar empresa

**Ejemplo POST /api/empresas**:
```json
{
    "nombre": "Empresa ABC",
    "nit": "900123456-1",
    "direccion": "Calle 123 #45-67",
    "telefono": "601-1234567",
    "email": "contacto@empresa.com"
}
```

### Representantes
- `GET /api/representantes` - Listar representantes
- `POST /api/representantes` - Crear representante
- `GET /api/representantes/{id}` - Obtener representante
- `PUT /api/representantes/{id}` - Actualizar representante
- `DELETE /api/representantes/{id}` - Eliminar representante

**Ejemplo POST /api/representantes**:
```json
{
    "nombre": "Carlos",
    "apellido": "Rodríguez",
    "documento": "12345678",
    "telefono": "300-1111111",
    "email": "carlos@empresa.com",
    "empresa_id": 1
}
```

### Categorías de Maquinaria
- `GET /api/categorias` - Listar categorías
- `POST /api/categorias` - Crear categoría
- `GET /api/categorias/{id}` - Obtener categoría
- `PUT /api/categorias/{id}` - Actualizar categoría
- `DELETE /api/categorias/{id}` - Eliminar categoría

### Tipos de Maquinaria
- `GET /api/tipos-maquinaria` - Listar tipos
- `POST /api/tipos-maquinaria` - Crear tipo
- `GET /api/tipos-maquinaria/{id}` - Obtener tipo
- `PUT /api/tipos-maquinaria/{id}` - Actualizar tipo
- `DELETE /api/tipos-maquinaria/{id}` - Eliminar tipo

### Mantenimientos
- `GET /api/mantenimientos` - Listar mantenimientos
- `POST /api/mantenimientos` - Crear mantenimiento
- `GET /api/mantenimientos/{id}` - Obtener mantenimiento
- `PUT /api/mantenimientos/{id}` - Actualizar mantenimiento
- `DELETE /api/mantenimientos/{id}` - Eliminar mantenimiento

**Ejemplo POST /api/mantenimientos**:
```json
{
    "codigo": "MANT-001",
    "nombre": "Mantenimiento Preventivo",
    "descripcion": "Mantenimiento preventivo completo",
    "costo": 1500000,
    "tipo_maquinaria_id": 1
}
```

### Solicitudes
- `GET /api/solicitudes` - Listar solicitudes
- `POST /api/solicitudes` - Crear solicitud
- `GET /api/solicitudes/{id}` - Obtener solicitud
- `PUT /api/solicitudes/{id}` - Actualizar solicitud
- `DELETE /api/solicitudes/{id}` - Eliminar solicitud

**Ejemplo POST /api/solicitudes**:
```json
{
    "codigo": "SOL-001",
    "fecha_solicitud": "2023-10-15",
    "estado": "pendiente",
    "observaciones": "Mantenimiento urgente",
    "empresa_id": 1
}
```

### Detalles de Solicitud
- `GET /api/detalle-solicitudes` - Listar detalles
- `POST /api/detalle-solicitudes` - Crear detalle
- `GET /api/detalle-solicitudes/{id}` - Obtener detalle
- `PUT /api/detalle-solicitudes/{id}` - Actualizar detalle
- `DELETE /api/detalle-solicitudes/{id}` - Eliminar detalle

**Ejemplo POST /api/detalle-solicitudes**:
```json
{
    "solicitud_id": 1,
    "mantenimiento_id": 1,
    "cantidad_maquinas": 2
}
```

### Empleados
- `GET /api/empleados` - Listar empleados
- `POST /api/empleados` - Crear empleado
- `GET /api/empleados/{id}` - Obtener empleado
- `PUT /api/empleados/{id}` - Actualizar empleado
- `DELETE /api/empleados/{id}` - Eliminar empleado

### Asignación de Empleados
- `GET /api/solicitud-empleados` - Listar asignaciones
- `POST /api/solicitud-empleados` - Crear asignación
- `GET /api/solicitud-empleados/{id}` - Obtener asignación
- `PUT /api/solicitud-empleados/{id}` - Actualizar asignación
- `DELETE /api/solicitud-empleados/{id}` - Eliminar asignación

## Endpoints de Consultas Especiales

### Consultas Generales
- `GET /api/consultas/empleados-ordenados` - Empleados ordenados por apellido
- `GET /api/consultas/maquinaria-pesada-costosa` - Maquinaria pesada con costo > 1M
- `GET /api/consultas/empresa-mas-solicitudes` - Empresa con más solicitudes
- `GET /api/consultas/maquinas-argos` - Total máquinas de empresa Argos
- `GET /api/consultas/solicitudes-empleado` - Solicitudes del empleado específico
- `GET /api/consultas/representantes-sin-solicitudes` - Representantes sin solicitudes
- `GET /api/consultas/listado-solicitudes` - Listado completo de solicitudes
- `POST /api/consultas/solicitud-por-codigo` - Buscar solicitud por código
- `GET /api/consultas/mantenimientos-retroexcavadoras` - Mantenimientos de retroexcavadoras
- `GET /api/consultas/solicitudes-octubre-2023` - Solicitudes de octubre 2023

**Ejemplo POST /api/consultas/solicitud-por-codigo**:
```json
{
    "codigo": "SOL-001"
}
```

### Dashboard y Estadísticas
- `GET /api/dashboard/stats` - Estadísticas generales
- `GET /api/dashboard/solicitudes-por-estado` - Solicitudes agrupadas por estado
- `GET /api/dashboard/top-empresas` - Top 5 empresas con más solicitudes

## Códigos de Estado HTTP

- `200` - OK (Éxito)
- `201` - Created (Creado exitosamente)
- `400` - Bad Request (Error en la solicitud)
- `401` - Unauthorized (No autorizado)
- `404` - Not Found (No encontrado)
- `422` - Unprocessable Entity (Error de validación)
- `500` - Internal Server Error (Error del servidor)

## Formato de Respuesta

### Respuesta Exitosa
```json
{
    "status": true,
    "message": "Operación exitosa",
    "data": {
        // Datos de la respuesta
    }
}
```

### Respuesta de Error
```json
{
    "status": false,
    "message": "Mensaje de error",
    "errors": {
        // Detalles de errores de validación
    }
}
```

## Datos de Prueba Incluidos

El seeder incluye los siguientes datos de prueba:

### Usuarios
- `admin@example.com` / `password` (rol: admin)
- `juan@example.com` / `password` (rol: empleado)
- `maria@example.com` / `password` (rol: empleado)

### Empresas
- Argos S.A.
- Constructora ABC
- Ingeniería XYZ

### Categorías de Maquinaria
- Maquinaria Pesada
- Maquinaria Ligera

### Tipos de Maquinaria
- Retroexcavadora
- Excavadora
- Bulldozer
- Vibrador de Concreto

### Mantenimientos
- MANT-001: Mantenimiento Preventivo Retroexcavadora ($1,500,000)
- MANT-002: Reparación Sistema Hidráulico ($2,500,000)
- MANT-003: Cambio de Aceite y Filtros ($800,000)
- MANT-004: Calibración Vibrador ($300,000)

## Configuración de Postman

1. **Crear una nueva colección** llamada "API Maquinaria"
2. **Configurar variables de entorno**:
   - `base_url`: `http://localhost:8000/api`
   - `token`: (se llenará automáticamente después del login)
3. **Configurar headers globales**:
   - `Content-Type`: `application/json`
   - `Accept`: `application/json`
4. **Para endpoints protegidos**, agregar header:
   - `Authorization`: `Bearer {{token}}`

## Flujo de Prueba Recomendado

1. **Registrar usuario** o hacer **login**
2. **Copiar el token** de la respuesta
3. **Configurar el token** en las variables de entorno
4. **Probar endpoints CRUD** en orden lógico:
   - Empresas → Representantes → Categorías → Tipos → Mantenimientos → Solicitudes → Detalles → Empleados → Asignaciones
5. **Probar consultas especiales**
6. **Probar dashboard y estadísticas**





