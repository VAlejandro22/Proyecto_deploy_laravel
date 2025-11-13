# API de Pagos - Documentación

## Requisitos Funcionales Implementados

### 1. Facturación ✅
- Un usuario con rol `ventas` puede crear una factura a un cliente específico
- La factura contiene: cliente, monto total, fecha, estado (pendiente, pagada, anulada)

### 2. Registro de pago (API REST) ✅
- Usuarios con rol `cliente`, autenticados con token (auth:sanctum), pueden enviar pagos
- Endpoint: `POST /api/cliente/pagos`
- Campos requeridos:
  - ID de la factura
  - Tipo de pago (efectivo, tarjeta, transferencia, cheque)
  - Número de transacción o comprobante
  - Monto pagado
  - Observaciones (opcional)
- El estado del pago será `pendiente` hasta ser validado

### 3. Validación de pago (Web) ✅
- Usuarios con rol `pagos`, autenticados vía Breeze (auth:web), pueden:
  - Ver listado de pagos pendientes: `/pagos`
  - Aprobar o rechazar el pago
- Al aprobar el pago:
  - Estado del pago cambia a `aprobado`
  - Estado de la factura cambia a `pagada`
- Al rechazar:
  - Estado del pago cambia a `rechazado`
  - Estado de la factura se mantiene `pendiente`

## Endpoints de la API

### Autenticación
Todos los endpoints requieren autenticación con Sanctum:
```
Authorization: Bearer {tu_token_aqui}
```

### 1. Crear un Pago
```http
POST /api/cliente/pagos
Content-Type: application/json
Authorization: Bearer {token_cliente}

{
    "factura_id": 1,
    "tipo_pago": "transferencia",
    "numero_transaccion": "TXN123456789",
    "monto_pagado": 150.50,
    "observaciones": "Pago realizado mediante transferencia bancaria"
}
```

**Respuesta exitosa (201):**
```json
{
    "success": true,
    "message": "Pago registrado exitosamente. Pendiente de validación.",
    "data": {
        "pago_id": 1,
        "factura_numero": "FAC-000001",
        "monto_pagado": 150.50,
        "estado": "pendiente"
    }
}
```

### 2. Listar Pagos del Cliente
```http
GET /api/cliente/pagos
Authorization: Bearer {token_cliente}
```

**Parámetros opcionales:**
- `estado`: pendiente, aprobado, rechazado
- `factura_id`: ID de factura específica

### 3. Ver Detalles de un Pago
```http
GET /api/cliente/pagos/{id}
Authorization: Bearer {token_cliente}
```

## Rutas Web para Validación (Usuario con rol Pagos)

### 1. Ver Pagos Pendientes
```
GET /pagos
```

### 2. Ver Historial de Pagos
```
GET /pagos/historial
```

### 3. Ver Detalles de un Pago
```
GET /pagos/{id}
```

### 4. Aprobar un Pago
```
PATCH /pagos/{id}/aprobar
```

### 5. Rechazar un Pago
```
PATCH /pagos/{id}/rechazar
Content-Type: application/x-www-form-urlencoded

observaciones_rechazo=Motivo del rechazo
```

## Roles y Permisos

### Rol Cliente
- Puede crear pagos via API
- Solo puede ver sus propios pagos
- Solo puede pagar facturas asignadas a su cliente

### Rol Pagos
- Puede ver todos los pagos pendientes
- Puede aprobar/rechazar pagos
- Puede ver historial completo de pagos

### Rol Ventas
- Puede crear facturas con estado inicial `pendiente`

## Estados de Factura

- `pendiente`: Factura creada, esperando pago
- `pagada`: Factura completamente pagada
- `anulada`: Factura anulada
- `activa`: Estado legacy (compatible con sistema anterior)

## Estados de Pago

- `pendiente`: Pago registrado, esperando validación
- `aprobado`: Pago validado y aprobado
- `rechazado`: Pago rechazado por el validador

## Validaciones Importantes

1. **Monto de Pago**: No puede exceder el monto pendiente de la factura
2. **Propiedad**: Un cliente solo puede pagar facturas asignadas a él
3. **Estado de Factura**: Solo se pueden pagar facturas en estado `pendiente` o `activa`
4. **Pago Completo**: Cuando el total de pagos aprobados iguala el total de la factura, ésta cambia a `pagada`

## Ejemplo de Flujo Completo

1. **Usuario Ventas** crea una factura → Estado: `pendiente`
2. **Cliente** registra un pago via API → Estado del pago: `pendiente`
3. **Usuario Pagos** revisa y aprueba el pago → Estado del pago: `aprobado`, Estado de factura: `pagada`

## Errores Comunes

### 403 - No tiene permisos
- Usuario sin rol Cliente intentando crear pagos
- Cliente intentando pagar factura de otro cliente

### 400 - Datos inválidos
- Monto excede lo pendiente de pago
- Factura no disponible para pagos
- Campos requeridos faltantes

### 404 - No encontrado
- Factura no existe
- Pago no existe o no pertenece al cliente
