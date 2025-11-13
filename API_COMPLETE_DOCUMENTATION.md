# API Documentation - Sistema de Facturación

## Autenticación

Todas las rutas API requieren autenticación mediante Sanctum. Incluye el token en el header:

```
Authorization: Bearer {tu-token-aqui}
```

## Estructura de Respuesta

### Respuesta Exitosa
```json
{
    "success": true,
    "data": {...},
    "message": "Mensaje opcional"
}
```

### Respuesta con Error
```json
{
    "success": false,
    "message": "Descripción del error",
    "errors": {...} // Solo en errores de validación
}
```

### Respuesta con Paginación
```json
{
    "success": true,
    "data": [...],
    "pagination": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

## Endpoints por Módulo

### Dashboard
- **GET** `/api/dashboard` - Obtener datos del dashboard según el rol del usuario

### Usuarios (Solo Administradores)
- **GET** `/api/users` - Listar usuarios
- **POST** `/api/users` - Crear usuario
- **GET** `/api/users/{id}` - Ver usuario específico
- **PUT** `/api/users/{id}` - Actualizar usuario
- **DELETE** `/api/users/{id}` - Eliminar usuario
- **PATCH** `/api/users/{id}/toggle-status` - Cambiar estado del usuario
- **POST** `/api/users/{id}/tokens` - Crear token para usuario
- **DELETE** `/api/users/{id}/tokens/{tokenId}` - Eliminar token de usuario

### Clientes
- **GET** `/api/clientes` - Listar clientes (Administrador|Secretario|Ventas)
- **POST** `/api/clientes` - Crear cliente (Administrador|Secretario)
- **GET** `/api/clientes/{id}` - Ver cliente específico (Administrador|Secretario|Ventas)
- **PUT** `/api/clientes/{id}` - Actualizar cliente (Administrador|Secretario)
- **DELETE** `/api/clientes/{id}` - Eliminar cliente (Solo Administrador)
- **PATCH** `/api/clientes/{id}/toggle-status` - Cambiar estado del cliente (Administrador|Secretario)
- **GET** `/api/clientes/roles/available` - Obtener roles disponibles (Solo Administrador)
- **POST** `/api/clientes/{id}/assign-role` - Asignar rol a cliente (Solo Administrador)

### Productos
- **GET** `/api/productos` - Listar productos (Administrador|Bodega)
- **GET** `/api/productos/active` - Listar productos activos (Administrador|Bodega|Ventas)
- **POST** `/api/productos` - Crear producto (Administrador|Bodega)
- **GET** `/api/productos/{id}` - Ver producto específico (Administrador|Bodega)
- **PUT** `/api/productos/{id}` - Actualizar producto (Administrador|Bodega)
- **DELETE** `/api/productos/{id}` - Eliminar producto (Administrador|Bodega)
- **PATCH** `/api/productos/{id}/toggle-status` - Cambiar estado del producto (Administrador|Bodega)
- **POST** `/api/productos/{id}/check-stock` - Verificar stock (Administrador|Bodega|Ventas)

### Facturas
- **GET** `/api/facturas` - Listar facturas (según rol del usuario)
- **POST** `/api/facturas` - Crear factura (Administrador|Ventas)
- **GET** `/api/facturas/{id}` - Ver factura específica (según rol del usuario)
- **PUT** `/api/facturas/{id}` - Actualizar factura (Administrador|Ventas)
- **DELETE** `/api/facturas/{id}` - Eliminar factura (Solo Administrador)
- **PATCH** `/api/facturas/{id}/anular` - Anular factura (Administrador|Ventas)
- **GET** `/api/facturas/{id}/pdf` - Generar PDF de factura (según rol del usuario)
- **GET** `/api/facturas/stats` - Obtener estadísticas de facturas (según rol del usuario)

### API Tokens (Solo Administradores)
- **GET** `/api/api-tokens` - Listar todos los tokens
- **POST** `/api/api-tokens` - Crear token para cliente
- **DELETE** `/api/api-tokens/{clienteId}/{tokenId}` - Eliminar token de cliente

### Token del Usuario Actual
- **GET** `/api/my-token` - Obtener información del token actual
- **DELETE** `/api/my-token` - Revocar token actual
- **DELETE** `/api/my-token/all` - Revocar todos los tokens del usuario

### Rutas Específicas para Clientes
- **GET** `/api/cliente/facturas` - Listar facturas del cliente autenticado
- **GET** `/api/cliente/facturas/{id}` - Ver factura específica del cliente
- **GET** `/api/cliente/facturas-stats` - Estadísticas del cliente

## Parámetros de Filtrado

### Para Listados con Paginación
- `per_page` - Elementos por página (default: 15)
- `search` - Búsqueda por texto
- `status` - Filtrar por estado (activo/inactivo)

### Para Facturas
- `estado` - Filtrar por estado (activa/anulada)
- `fecha_desde` - Fecha desde (YYYY-MM-DD)
- `fecha_hasta` - Fecha hasta (YYYY-MM-DD)
- `cliente_id` - Filtrar por cliente específico

### Para Productos
- `categoria` - Filtrar por categoría

### Para Usuarios
- `role` - Filtrar por rol

## Ejemplos de Uso

### Crear Usuario
```bash
POST /api/users
Content-Type: application/json
Authorization: Bearer {token}

{
    "name": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role_id": 1,
    "activo": true
}
```

### Crear Cliente
```bash
POST /api/clientes
Content-Type: application/json
Authorization: Bearer {token}

{
    "nombre": "Empresa ABC",
    "email": "contacto@abc.com",
    "telefono": "123456789",
    "direccion": "Calle 123",
    "nit": "123456789-0",
    "activo": true
}
```

### Crear Producto
```bash
POST /api/productos
Content-Type: application/json
Authorization: Bearer {token}

{
    "nombre": "Producto Ejemplo",
    "descripcion": "Descripción del producto",
    "precio": 99.99,
    "stock": 100,
    "categoria": "Electrónicos",
    "codigo": "PROD001",
    "activo": true
}
```

### Crear Factura
```bash
POST /api/facturas
Content-Type: application/json
Authorization: Bearer {token}

{
    "cliente_id": 1,
    "productos": [
        {
            "id": 1,
            "cantidad": 2,
            "precio_unitario": 99.99
        },
        {
            "id": 2,
            "cantidad": 1,
            "precio_unitario": 49.99
        }
    ],
    "observaciones": "Factura de prueba"
}
```

## Códigos de Estado HTTP

- **200** - Éxito
- **201** - Creado exitosamente
- **400** - Solicitud incorrecta
- **401** - No autenticado
- **403** - No autorizado (sin permisos)
- **404** - No encontrado
- **409** - Conflicto (ej: dependencias al eliminar)
- **422** - Error de validación
- **500** - Error interno del servidor

## Roles y Permisos

### Administrador
- Acceso completo a todas las funcionalidades
- Puede gestionar usuarios, clientes, productos y facturas
- Puede eliminar registros
- Puede gestionar tokens de API

### Ventas
- Puede ver y gestionar clientes
- Puede crear, editar y anular facturas
- Puede ver productos activos

### Secretario
- Puede gestionar clientes
- Puede ver facturas

### Bodega
- Puede gestionar productos
- Puede verificar stock

### Cliente
- Solo puede ver sus propias facturas
- Puede ver estadísticas personales
- Acceso limitado al dashboard

## Seguridad

- Todas las rutas requieren autenticación
- Se aplican middlewares de verificación de roles
- Se valida el estado del usuario (activo/inactivo)
- Los tokens tienen capacidades específicas configurables
- Los clientes solo pueden acceder a sus propios datos
