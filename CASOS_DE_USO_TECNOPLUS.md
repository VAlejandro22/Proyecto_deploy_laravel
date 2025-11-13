# Casos de Uso – TECNOPLUS

Versión: 1.0  
Fecha: 2025-10-27


---

## UC-01 — Crear Factura (Rol: Ventas)

### Propósito y alcance
Emitir una factura en estado inicial "pendiente" para un cliente, con productos y cantidades, y disponer del PDF.

### Actores
- Primario: Usuario con rol Ventas  
- Secundarios: Administrador (permite también), Sistema (inventario/validaciones)

### Precondiciones
- Usuario autenticado y activo con permiso de creación de facturas.  
- Existen Cliente y Productos activos.  
- Stock suficiente para cada producto.

### Postcondiciones
- Factura creada en estado "pendiente".  
- PDF disponible para descarga.

### Reglas de negocio
- <a id="uc01-rb1"></a>RB-01-UC01: El producto debe estar activo para ser facturado.  
- <a id="uc01-rb2"></a>RB-02-UC01: Debe existir stock suficiente para la cantidad solicitada.  
- <a id="uc01-rb3"></a>RB-03-UC01: Los totales se calculan sumando (cantidad × precio) por ítem.  
- <a id="uc01-rb4"></a>RB-04-UC01: Sólo roles autorizados (Ventas/Administrador) pueden crear facturas.

### Flujo normal
- <a id="uc01-fn-1"></a>1. El usuario Ventas accede a "Nueva Factura".  
- <a id="uc01-fn-2"></a>2. Selecciona un cliente válido. Si no, [ver A2](#uc01-a2-datos-de-cliente-invalidos) y [RB-04](#uc01-rb4).  
- <a id="uc01-fn-3"></a>3. Agrega uno o más productos activos con cantidades. Si un producto inactivo o con stock insuficiente, ir a [A1](#uc01-a1-stock-insuficiente) y [RB-01](#uc01-rb1)/[RB-02](#uc01-rb2).  
- <a id="uc01-fn-4"></a>4. El sistema calcula subtotales y total según [RB-03](#uc01-rb3).  
- <a id="uc01-fn-5"></a>5. El usuario confirma la creación.  
- <a id="uc01-fn-6"></a>6. El sistema registra la factura en estado "pendiente".  
- <a id="uc01-fn-7"></a>7. El usuario puede descargar el PDF de la factura.

### Flujos alternos
- <a id="uc01-a1-stock-insuficiente"></a>A1. Stock insuficiente o producto inactivo  
  1. El sistema detecta que el producto está inactivo o sin stock ([RB-01](#uc01-rb1), [RB-02](#uc01-rb2)).  
  2. Muestra mensaje indicando el ítem afectado.  
  3. El usuario ajusta cantidades o reemplaza el producto.  
  4. Regresa al [Paso 3](#uc01-fn-3) del Flujo Normal.

- <a id="uc01-a2-datos-de-cliente-invalidos"></a>A2. Datos de cliente inválidos  
  1. El sistema valida que el cliente esté activo y con datos obligatorios.  
  2. Si hay inconsistencias, muestra errores.  
  3. El usuario corrige o selecciona otro cliente.  
  4. Regresa al [Paso 2](#uc01-fn-2) del Flujo Normal.

- <a id="uc01-a3-permisos-o-sesion"></a>A3. Sesión expirada o permisos insuficientes  
  1. El sistema detecta inactividad o falta de rol/permiso ([RB-04](#uc01-rb4)).  
  2. Redirige a autenticación o muestra 403.  
  3. Tras autenticarse o corregir permisos, retorna al [Paso 1](#uc01-fn-1).


---

## UC-02 — Registrar Pago vía API (Rol: Cliente)

### Propósito y alcance
Permitir a un Cliente registrar un pago contra una de sus facturas mediante token Bearer (Sanctum).

### Actores
- Primario: Cliente (API)  
- Secundarios: Sistema (validaciones), Rol Pagos (valida posteriormente)

### Precondiciones
- Token válido (usuario activo).  
- La factura pertenece al cliente.  
- Factura en estado pagable (pendiente/activa).

### Postcondiciones
- Pago creado en estado "pendiente" y asociado a la factura y cliente.

### Reglas de negocio
- <a id="uc02-rb1"></a>RB-01-UC02: Un cliente sólo puede pagar sus propias facturas (ver SRS RB-01).  
- <a id="uc02-rb2"></a>RB-02-UC02: Sólo se pueden pagar facturas pendientes o activas (SRS RB-02).  
- <a id="uc02-rb3"></a>RB-03-UC02: El monto pagado no puede exceder el saldo pendiente (SRS RB-03).  
- <a id="uc02-rb4"></a>RB-04-UC02: Se requiere token válido (Sanctum) y usuario activo.

### Flujo normal
- <a id="uc02-fn-1"></a>1. El cliente prepara la solicitud POST a `/api/cliente/pagos` con los campos requeridos.  
- <a id="uc02-fn-2"></a>2. El sistema valida token y propiedad de la factura ([RB-01-UC02](#uc02-rb1), [RB-04-UC02](#uc02-rb4)).  
- <a id="uc02-fn-3"></a>3. El sistema valida estado de factura ([RB-02-UC02](#uc02-rb2)). Si no, [A3](#uc02-a3-factura-no-pagable).  
- <a id="uc02-fn-4"></a>4. El sistema valida monto ≤ saldo ([RB-03-UC02](#uc02-rb3)). Si no, [A1](#uc02-a1-monto-excedido).  
- <a id="uc02-fn-5"></a>5. Registra el pago en estado "pendiente" y retorna 201 con datos básicos.

### Flujos alternos
- <a id="uc02-a1-monto-excedido"></a>A1. Monto excedido  
  1. El sistema detecta monto mayor al saldo ([RB-03-UC02](#uc02-rb3)).  
  2. Responde 400/422 con mensaje.  
  3. El cliente ajusta el monto y reintenta → [Paso 4](#uc02-fn-4).

- <a id="uc02-a2-token-invalido"></a>A2. Token inválido o usuario inactivo  
  1. El sistema rechaza con 401/403 ([RB-04-UC02](#uc02-rb4)).  
  2. El cliente corrige credenciales y reintenta → [Paso 1](#uc02-fn-1).

- <a id="uc02-a3-factura-no-pagable"></a>A3. Factura no pagable (no pendiente/activa)  
  1. El sistema rechaza el pago con mensaje.  
  2. El cliente verifica el estado de factura y reintenta → [Paso 3](#uc02-fn-3).


---

## UC-03 — Validar Pago (Rol: Pagos)

### Propósito y alcance
Permitir a un operador con rol Pagos aprobar o rechazar pagos pendientes y actualizar la factura.

### Actores
- Primario: Usuario con rol Pagos  
- Secundarios: Administrador (también puede), Sistema

### Precondiciones
- Usuario autenticado/activo con permiso de validación.  
- Existen pagos en estado "pendiente".

### Postcondiciones
- Pago queda en estado "aprobado" o "rechazado".  
- La factura puede pasar a "pagada" si corresponde.

### Reglas de negocio
- <a id="uc03-rb1"></a>RB-01-UC03: Si el total de pagos aprobados cubre el total de la factura, ésta pasa a "pagada" (SRS RB-04).  
- <a id="uc03-rb2"></a>RB-02-UC03: Un pago rechazado no altera el estado de factura (SRS RB-05).  
- <a id="uc03-rb3"></a>RB-03-UC03: Debe quedar auditoría de la acción (usuario, fecha/hora, motivo si rechazo).

### Flujo normal
- <a id="uc03-fn-1"></a>1. El operador accede a "Pagos pendientes".  
- <a id="uc03-fn-2"></a>2. Selecciona un pago para revisión.  
- <a id="uc03-fn-3"></a>3. Si es válido, lo aprueba → el pago queda "aprobado" y, si cubre total, la factura pasa a "pagada" ([RB-01-UC03](#uc03-rb1)).  
- <a id="uc03-fn-4"></a>4. El sistema registra auditoría ([RB-03-UC03](#uc03-rb3)).

### Flujos alternos
- <a id="uc03-a1-rechazar-pago"></a>A1. Rechazar pago  
  1. El operador decide rechazar el pago e ingresa motivo.  
  2. El sistema marca el pago como "rechazado" ([RB-02-UC03](#uc03-rb2)).  
  3. Regresa al [Paso 1](#uc03-fn-1) para continuar con otros pagos.

- <a id="uc03-a2-concurrencia"></a>A2. Concurrencia (pago ya procesado)  
  1. Al intentar aprobar/rechazar, el sistema detecta que el pago cambió de estado.  
  2. Muestra aviso y actualiza la vista.  
  3. El operador selecciona otro pago → [Paso 2](#uc03-fn-2).



---

## UC-04 — Gestionar Tokens API (Rol: Administrador)

### Propósito y alcance
Emitir, listar y revocar tokens API para clientes/usuarios, controlando el acceso a la integración.

### Actores
- Primario: Administrador  
- Secundarios: Usuario autenticado (revocación de su token actual)

### Precondiciones
- Administrador autenticado y activo.  
- Cliente/usuario objetivo existe.

### Postcondiciones
- Token emitido/listado/revocado según la acción.  
- Acceso API reflejado de inmediato al revocar.

### Reglas de negocio
- <a id="uc04-rb1"></a>RB-01-UC04: La emisión/revocación de tokens sólo puede realizarla un Administrador (SRS RB-08).  
- <a id="uc04-rb2"></a>RB-02-UC04: El token se muestra una sola vez al emitir (seguridad).  
- <a id="uc04-rb3"></a>RB-03-UC04: La revocación invalida inmediatamente el acceso asociado.

### Flujo normal
- <a id="uc04-fn-1"></a>1. El Administrador abre "API Tokens" en el dashboard.  
- <a id="uc04-fn-2"></a>2. Emite un nuevo token para un cliente/usuario válido ([RB-01-UC04](#uc04-rb1)).  
- <a id="uc04-fn-3"></a>3. El sistema muestra el token una sola vez ([RB-02-UC04](#uc04-rb2)).  
- <a id="uc04-fn-4"></a>4. El Administrador puede listar tokens existentes y revocar alguno ([RB-03-UC04](#uc04-rb3)).

### Flujos alternos
- <a id="uc04-a1-objetivo-invalido"></a>A1. Cliente/usuario objetivo inválido  
  1. El sistema muestra error de validación.  
  2. El Administrador corrige selección y regresa al [Paso 2](#uc04-fn-2).

- <a id="uc04-a2-token-ya-revocado"></a>A2. Token ya revocado/no existente  
  1. Al intentar revocar, el sistema informa que el token no es válido.  
  2. El Administrador selecciona otro → [Paso 4](#uc04-fn-4).

- <a id="uc04-a3-autogestion"></a>A3. Revocación del token actual por el usuario  
  1. Un usuario autenticado llama `DELETE /api/my-token`.  
  2. El sistema revoca el token actual ([RB-03-UC04](#uc04-rb3)).  
  3. Si requiere un nuevo token, el Administrador vuelve al [Paso 2](#uc04-fn-2).



---

