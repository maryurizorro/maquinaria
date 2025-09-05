# Guía de Consultas API para Postman

## Configuración Inicial

1. **Base URL**: `http://localhost:8000/api`
2. **Headers requeridos**:
   - `Content-Type: application/json`
   - `Accept: application/json`
   - `Authorization: Bearer {token}` (para rutas protegidas)

## Autenticación

### 1. Login
```
POST /api/login
Body (JSON):
{
    "email": "usuario@ejemplo.com",
    "password": "password123"
}
```

### 2. Obtener Token
Después del login, copia el token del response y úsalo en el header Authorization.

## Consultas Corregidas

### 1. Empleados Ordenados por Apellido
```
GET /api/consultas/empleados-ordenados
Headers: Authorization: Bearer {token}
```

### 2. Maquinaria Pesada Costosa (>1M)
```
GET /api/consultas/maquinaria-pesada-costosa
Headers: Authorization: Bearer {token}
```

### 3. Empresa con Más Solicitudes
```
GET /api/consultas/empresa-mas-solicitudes
Headers: Authorization: Bearer {token}
```

### 4. Máquinas de Empresa Argos
```
GET /api/consultas/maquinas-argos
Headers: Authorization: Bearer {token}
```

### 5. Solicitudes de Empleado Específico
```
GET /api/consultas/solicitudes-empleado
Headers: Authorization: Bearer {token}
```

### 6. Representantes sin Solicitudes
```
GET /api/consultas/representantes-sin-solicitudes
Headers: Authorization: Bearer {token}
```

### 7. Listado de Solicitudes
```
GET /api/consultas/listado-solicitudes
Headers: Authorization: Bearer {token}
```

### 8. Solicitud por Código
```
POST /api/consultas/solicitud-por-codigo
Headers: Authorization: Bearer {token}
Body (JSON):
{
    "codigo": "SOL-001"
}
```

### 9. Mantenimientos de Retroexcavadoras
```
GET /api/consultas/mantenimientos-retroexcavadoras
Headers: Authorization: Bearer {token}
```

### 10. Solicitudes de Octubre 2023
```
GET /api/consultas/solicitudes-octubre-2023
Headers: Authorization: Bearer {token}
```

## Consultas de Dashboard

### Estadísticas Generales
```
GET /api/dashboard/stats
Headers: Authorization: Bearer {token}
```

### Solicitudes por Estado
```
GET /api/dashboard/solicitudes-por-estado
Headers: Authorization: Bearer {token}
```

### Top 5 Empresas
```
GET /api/dashboard/top-empresas
Headers: Authorization: Bearer {token}
```

## Errores Corregidos

1. **Consulta de empleados**: Cambiado de modelo `User` a `Empleado`
2. **GroupBy en consultas**: Especificados todos los campos en el GROUP BY
3. **Relaciones**: Mejoradas las consultas con relaciones usando `with()`
4. **Validación**: Agregada validación en consulta por código
5. **Select específico**: Especificados campos exactos en lugar de `*`

## Respuestas Esperadas

Todas las consultas devuelven un formato consistente:

```json
{
    "status": true,
    "data": [
        // datos de la consulta
    ]
}
```

En caso de error:
```json
{
    "status": false,
    "message": "Mensaje de error"
}
```
