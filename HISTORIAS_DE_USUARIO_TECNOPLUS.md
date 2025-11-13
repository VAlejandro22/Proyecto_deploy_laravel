# Historias de Usuario – TECNOPLUS

Versión: 1.0  
Fecha: 2025-10-27

Este documento contiene 4 historias de usuario priorizadas y trazables al SRS. Cada historia incluye criterios de aceptación en formato Gherkin, alcance, supuestos, dependencias, prioridades y definición de terminado.

Referencia SRS: `ESPECIFICACION_REQUERIMIENTOS_TECNOPLUS.md`

---

## HU-01: Crear factura desde el Dashboard (Rol Ventas)

Como usuario con rol Ventas  
Quiero crear una factura para un cliente con productos y cantidades  
Para registrar una venta y generar el documento correspondiente (PDF disponible)

- Prioridad: Alta  
- Puntos de historia: 5  
- Actores: Ventas, Administrador (permite también)  
- Trazabilidad SRS: RF-040, RF-041, RF-042, RNF-030  
- Supuestos: Existen clientes y productos activos con stock suficiente; el usuario está autenticado y activo.

### Criterios de aceptación (Gherkin)
1. Flujo feliz
   - Dado que estoy autenticado con rol Ventas y en la pantalla "Nueva factura"
   - Cuando selecciono un cliente válido y agrego uno o más productos activos con cantidades válidas
   - Entonces el sistema calcula subtotales, impuestos/total y me permite guardar la factura en estado "pendiente".

2. Stock insuficiente
   - Dado que intento agregar un producto con stock menor a la cantidad solicitada
   - Cuando confirmo la creación de la factura
   - Entonces el sistema bloquea la operación y muestra un mensaje claro indicando el producto y la disponibilidad.

3. PDF disponible
   - Dado que existe una factura creada correctamente
   - Cuando navego a la acción "Descargar PDF" de la factura
   - Entonces el sistema genera y descarga el PDF con los datos actuales de la factura.

### Definición de Terminado (DoD)
- Validaciones de cliente/productos y stock implementadas.
- Totales calculados correctamente (sumas, impuestos si aplica).
- Factura persiste en BD en estado inicial pendiente.
- Enlace/acción para generar PDF operativo y probado.
- Pruebas funcionales básicas (crear, ver, PDF) documentadas.

---

## HU-02: Registrar pago vía API (Rol Cliente, Token Sanctum)

Como Cliente autenticado mediante token Bearer (Sanctum)  
Quiero registrar un pago contra una de mis facturas  
Para iniciar el proceso de validación y liquidar mi deuda

- Prioridad: Alta  
- Puntos de historia: 8  
- Actores: Cliente (API), Pagos (valida)  
- Trazabilidad SRS: RF-050, RF-051, RNF-001, RNF-002, RNF-040  
- Supuestos: La factura pertenece al cliente y está en estado pagable (pendiente/activa).

### Criterios de aceptación (Gherkin)
1. Registro exitoso
   - Dado que poseo un token válido y una factura pendiente de mi propiedad
   - Cuando envío un POST a `/api/cliente/pagos` con `factura_id`, `tipo_pago`, `numero_transaccion`, `monto_pagado` y `observaciones` (opcional)
   - Entonces el sistema crea el pago en estado `pendiente` y responde 201 con datos básicos del pago.

2. Monto excedido
   - Dado que el monto pagado supera el saldo pendiente de la factura
   - Cuando envío el POST
   - Entonces el sistema responde 400/422 con un mensaje indicando que el monto excede el saldo.

3. Propiedad y autenticación
   - Dado que intento pagar una factura que no es mía o sin token válido
   - Cuando envío el POST
   - Entonces el sistema responde 401 (sin token o inválido) o 403 (sin permiso/propiedad).

### Definición de Terminado (DoD)
- Validaciones: token, propiedad de factura, estados permitidos, monto ≤ saldo.
- Respuestas consistentes con estructura de API (success, message, data).
- Auditoría básica del registro (timestamps, user/cliente asociado).
- Pruebas de contrato con ejemplos válidos e inválidos.

---

## HU-03: Validar (aprobar/rechazar) pagos en Dashboard (Rol Pagos)

Como usuario con rol Pagos  
Quiero revisar pagos pendientes y aprobar o rechazar  
Para confirmar o descartar pagos reportados por clientes y actualizar el estado de la factura

- Prioridad: Alta  
- Puntos de historia: 5  
- Actores: Pagos, Administrador  
- Trazabilidad SRS: RF-052, RB-03, RB-04, RB-05, RNF-030  
- Supuestos: Existen pagos en estado pendiente y acceso con rol Pagos.

### Criterios de aceptación (Gherkin)
1. Aprobar pago
   - Dado que estoy autenticado con rol Pagos y visualizo un pago pendiente
   - Cuando lo apruebo
   - Entonces el pago cambia a `aprobado` y la factura asociada cambia a `pagada` si el total aprobado alcanza el total de la factura.

2. Rechazar pago
   - Dado que estoy autenticado con rol Pagos y visualizo un pago pendiente
   - Cuando lo rechazo indicando un motivo
   - Entonces el pago cambia a `rechazado` y la factura mantiene su estado previo.

3. Trazabilidad
   - Dado que ejecuto una acción de aprobación o rechazo
   - Cuando la operación concluye
   - Entonces queda registro de usuario operador, fecha/hora y motivo (si rechazo).

### Definición de Terminado (DoD)
- Listados de pagos: pendientes e historial.
- Acciones aprobar/rechazar con confirmación y mensajes de resultado.
- Regla de negocio de actualización de factura aplicada.
- Registro de auditoría mínimo.
- Pruebas funcionales de ambos caminos (aprobación/rechazo).

---

## HU-04: Gestionar tokens API de clientes (Rol Administrador)

Como Administrador  
Quiero emitir, listar y revocar tokens de API para clientes  
Para habilitar o retirar el acceso de integración a la API de forma controlada

- Prioridad: Media-Alta  
- Puntos de historia: 3  
- Actores: Administrador; Usuario autenticado (revocar su propio token actual)  
- Trazabilidad SRS: RF-060, RNF-001, RNF-002  
- Supuestos: Los clientes existen y pueden asociarse a usuarios para el acceso API.

### Criterios de aceptación (Gherkin)
1. Emisión de token
   - Dado que estoy autenticado como Administrador
   - Cuando genero un token para un cliente válido
   - Entonces el sistema crea el token, lo muestra una sola vez y lo deja activo para su uso inmediato.

2. Listado y revocación
   - Dado que estoy autenticado como Administrador
   - Cuando consulto los tokens de un cliente
   - Entonces visualizo los tokens activos y puedo revocarlos
   - Y al revocar, el acceso por ese token queda invalidado inmediatamente.

3. Autogestión del token actual
   - Dado que un usuario autenticado desea revocar su token actual
   - Cuando llama a `/api/my-token` DELETE
   - Entonces su token queda invalidado y no puede reutilizarse.

### Definición de Terminado (DoD)
- Formularios/vistas en dashboard para emisión/listado/revocación.
- Endpoints de soporte en API protegidos por rol.
- Revocación efectiva e inmediata (no permite llamadas posteriores con el token revocado).
- Mensajería clara y registros básicos en logs.

---

## Notas generales
- Accesibilidad: mensajes de error claros y validaciones visibles (RNF-030).
- Seguridad: todas las acciones protegidas por autenticación y autorización acorde al rol/permiso (RNF-001, RNF-002).
- Rendimiento: listados paginados y filtrables cuando aplique (RNF-010).
- Interoperabilidad: API JSON, encabezados estándar, soporte a Bearer Token (RNF-040).
