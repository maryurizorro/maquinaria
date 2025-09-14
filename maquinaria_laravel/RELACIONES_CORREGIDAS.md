# Relaciones de Base de Datos Corregidas - Sistema MY SAS

## Estructura de Tablas y Relaciones

### 1. **EMPRESAS** (Tabla principal de empresas contratistas)
- **Campos:**
  - `id` (Primary Key)
  - `nombre` (Razón Social)
  - `nit` (NIT único)
  - `direccion` (Dirección)
  - `ciudad` (Ciudad) ✅ **AGREGADO**
  - `telefono` (Teléfono)
  - `email` (Email único)
  - `timestamps`

### 2. **REPRESENTANTES** (Representante legal de cada empresa)
- **Campos:**
  - `id` (Primary Key)
  - `nombre` (Nombre del representante)
  - `apellido` (Apellido del representante)
  - `documento` (Documento único)
  - `telefono` (Teléfono)
  - `email` (Email único)
  - `empresa_id` (Foreign Key a empresas) ✅ **RELACIÓN 1:1**
  - `timestamps`

### 3. **CATEGORIA_MAQUINARIAS** (Categorías: Ligera y Pesada)
- **Campos:**
  - `id` (Primary Key)
  - `nombre` (Ligera/Pesada)
  - `descripcion` (Descripción de la categoría)
  - `timestamps`

### 4. **TIPO_MAQUINARIAS** (Tipos específicos de maquinaria)
- **Campos:**
  - `id` (Primary Key)
  - `nombre` (Ej: Excavadora, Retroexcavadora, etc.)
  - `descripcion` (Descripción del tipo)
  - `categoria_id` (Foreign Key a categoria_maquinarias)
  - `timestamps`

### 5. **MANTENIMIENTOS** (Servicios de mantenimiento específicos por tipo)
- **Campos:**
  - `id` (Primary Key)
  - `codigo` (Código único del mantenimiento)
  - `nombre` (Nombre del mantenimiento)
  - `descripcion` (Descripción del mantenimiento)
  - `costo` (Costo de mano de obra)
  - `tiempo_estimado` (Tiempo estimado en horas) ✅ **AGREGADO**
  - `manual_procedimiento` (Manual de procedimiento) ✅ **AGREGADO**
  - `tipo_maquinaria_id` (Foreign Key a tipo_maquinarias) ✅ **ESPECÍFICO POR TIPO**
  - `timestamps`

### 6. **SOLICITUDS** (Solicitudes de mantenimiento)
- **Campos:**
  - `id` (Primary Key)
  - `codigo` (Código único de la solicitud)
  - `fecha_solicitud` (Fecha de la solicitud)
  - `estado` (pendiente, en_proceso, completada, cancelada)
  - `observaciones` (Observaciones generales)
  - `descripcion_solicitud` (Descripción específica del mantenimiento solicitado) ✅ **AGREGADO**
  - `fecha_deseada` (Fecha deseada para el mantenimiento) ✅ **AGREGADO**
  - `empresa_id` (Foreign Key a empresas)
  - `timestamps`

### 7. **DETALLE_SOLICITUDS** (Detalles específicos de cada solicitud)
- **Campos:**
  - `id` (Primary Key)
  - `solicitud_id` (Foreign Key a solicituds)
  - `mantenimiento_id` (Foreign Key a mantenimientos)
  - `cantidad_maquinas` (Cantidad de máquinas)
  - `costo_total` (Costo total del detalle)
  - `Url_foto` (URL de la foto del mantenimiento)
  - `timestamps`

### 8. **EMPLEADOS** (Empleados de MY SAS)
- **Campos:**
  - `id` (Primary Key)
  - `nombre` (Nombre del empleado)
  - `apellido` (Apellido del empleado)
  - `documento` (Documento único)
  - `email` (Email único)
  - `direccion` (Dirección)
  - `telefono` (Teléfono)
  - `rol` (admin, empleado, supervisor)
  - `timestamps`

### 9. **SOLICITUD_EMPLEADOS** (Tabla pivot para asignación de empleados)
- **Campos:**
  - `id` (Primary Key)
  - `solicitud_id` (Foreign Key a solicituds)
  - `empleado_id` (Foreign Key a empleados)
  - `estado` (asignado, en_proceso, completado)
  - `timestamps`

## Relaciones Implementadas

### ✅ **Relaciones Principales:**

1. **EMPRESA ↔ REPRESENTANTE** (1:1)
   - Una empresa tiene UN representante legal
   - Un representante pertenece a UNA empresa

2. **CATEGORIA_MAQUINARIA ↔ TIPO_MAQUINARIA** (1:N)
   - Una categoría tiene muchos tipos de maquinaria
   - Un tipo pertenece a una categoría

3. **TIPO_MAQUINARIA ↔ MANTENIMIENTO** (1:N)
   - Un tipo de maquinaria tiene muchos mantenimientos específicos
   - Un mantenimiento pertenece a UN tipo específico ✅ **CORREGIDO**

4. **EMPRESA ↔ SOLICITUD** (1:N)
   - Una empresa puede hacer muchas solicitudes
   - Una solicitud pertenece a una empresa

5. **SOLICITUD ↔ DETALLE_SOLICITUD** (1:N)
   - Una solicitud puede tener muchos detalles
   - Un detalle pertenece a una solicitud

6. **MANTENIMIENTO ↔ DETALLE_SOLICITUD** (1:N)
   - Un mantenimiento puede estar en muchos detalles
   - Un detalle tiene un mantenimiento específico

7. **SOLICITUD ↔ EMPLEADO** (N:N)
   - Una solicitud puede ser asignada a varios empleados
   - Un empleado puede atender varias solicitudes

## Cambios Implementados

### ✅ **Campos Agregados:**
- `empresas.ciudad` - Ciudad de la empresa
- `mantenimientos.tiempo_estimado` - Tiempo estimado en horas
- `mantenimientos.manual_procedimiento` - Manual de procedimiento
- `solicituds.descripcion_solicitud` - Descripción específica del mantenimiento
- `solicituds.fecha_deseada` - Fecha deseada para el mantenimiento

### ✅ **Relaciones Corregidas:**
- **Mantenimiento específico por tipo:** Cada mantenimiento está vinculado a UN tipo específico de maquinaria, no puede aplicarse a diferentes tipos
- **Empresa-Representante 1:1:** Cada empresa tiene un representante legal único
- **Integridad de datos:** Se mantiene la consistencia en las relaciones

## Código en Español

Todos los comentarios, nombres de campos y documentación están en español para facilitar el entendimiento del sistema.

## Funcionalidades del Sistema

1. **Registro de empresas** con todos los datos requeridos incluyendo ciudad
2. **Gestión de representantes legales** (uno por empresa)
3. **Categorización de maquinaria** (Ligera y Pesada)
4. **Tipos específicos de maquinaria** por categoría
5. **Mantenimientos específicos** por tipo de maquinaria con costo, tiempo y procedimiento únicos
6. **Solicitudes de mantenimiento** con descripción y fecha deseada
7. **Asignación de empleados** a solicitudes
8. **Seguimiento fotográfico** de los mantenimientos

El sistema ahora cumple con todos los requerimientos del documento GD-F-007 V04.
