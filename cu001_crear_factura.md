# TECNOPLUS
## DIRECCIÓN DE TECNOLOGÍA
### SISTEMA DE FACTURACIÓN Y PAGOS

---

## CASO DE USO: CREAR FACTURA
**Versión:** 1.0  
**Código:** CU-001  
**Fecha:** 28/10/2025  
**Confidencial** © Dirección de Tecnología - TECNOPLUS

---

## Información General

| **TÍTULO** | Caso de Uso - Crear Factura |
|------------|----------------------------|
| **SUBTÍTULO** | Sistema de Facturación y Pagos TECNOPLUS |
| **VERSIÓN** | 1.0.0 |
| **ARCHIVO** | CU-001-Crear-Factura.docx |
| **AUTOR** | Equipo de Ingeniería de Requisitos - GR02 |
| **ESTADO** | Aprobado |

---

## Firmas y Aprobaciones

| **ELABORADO POR** | Equipo de Ingeniería de Requisitos - GR02 |
|-------------------|------------------------------------------|
| **FECHA** | 2025-10-28 |
| **FIRMA** | _________________ |

| **REVISADO POR** | Ing. Marcelo Rea - Docente |
|------------------|----------------------------|
| **FECHA** | 2025-10-28 |
| **FIRMA** | _________________ |

| **APROBADO POR** | Gerente General - TECNOPLUS |
|------------------|----------------------------|
| **FECHA** | 2025-10-28 |
| **FIRMA** | _________________ |

---

## Lista de Cambios

| **VERSIÓN** | **FECHA** | **AUTOR** | **DESCRIPCIÓN** |
|-------------|-----------|-----------|-----------------|
| 1.0.0 | 2025-10-28 | GR02 | Emisión Inicial |

---

## Tabla de Contenidos

1. Identificador
2. Nombre del Caso de Uso
3. Breve Descripción
4. Definiciones, Acrónimos y Abreviaturas
5. Actores y Roles
6. Precondiciones
7. Flujo de Eventos
   - 7.1 Flujo Principal
   - 7.2 Sub-Flujos
   - 7.3 Flujos Alternos
8. Reglas del Negocio
9. Poscondiciones
10. Requerimientos Especiales
11. Relaciones con Otros Casos de Uso
12. Modelamiento
    - 12.1 Diagrama de Caso de Uso (UML)
    - 12.2 Diagrama de Actividades
13. Anexos

---

## 1. IDENTIFICADOR

**CU-001**

---

## 2. NOMBRE DEL CASO DE USO

**Crear Factura**

---

## 3. BREVE DESCRIPCIÓN

Este caso de uso permite al personal de Ventas y al Administrador crear facturas electrónicas para los clientes de TECNOPLUS. El sistema registra los productos solicitados por el cliente, calcula automáticamente los valores (subtotal, IVA y total), genera una numeración secuencial única, y emite la factura en formato PDF cumpliendo con las normativas del Servicio de Rentas Internas (SRI) de Ecuador.

El caso de uso asegura que:
- Solo se facturen productos disponibles en inventario
- El cliente esté activo en el sistema
- Se aplique correctamente el IVA según normativa vigente (15%)
- Se genere la documentación necesaria para auditoría y cumplimiento tributario

---

## 4. DEFINICIONES, ACRÓNIMOS Y ABREVIATURAS

| **TÉRMINO/ABREVIATURA** | **DESCRIPCIÓN** |
|-------------------------|-----------------|
| **API** | Application Programming Interface - Interfaz de Programación de Aplicaciones |
| **IVA** | Impuesto al Valor Agregado. Impuesto que se aplica sobre los productos vendidos (actualmente 15% en Ecuador) |
| **PDF** | Portable Document Format - Formato de documento portátil |
| **SRI** | Servicio de Rentas Internas. Entidad encargada de la regulación tributaria y facturación electrónica en Ecuador |
| **RUC** | Registro Único de Contribuyentes. Identificación tributaria en Ecuador |
| **CI** | Cédula de Identidad |
| **Stock** | Cantidad de productos disponibles en inventario |
| **Numeración Secuencial** | Serie consecutiva e irrepetible de números asignados a las facturas |

---

## 5. ACTORES Y ROLES

### 5.1 Actores

**Actor Principal:**
- **Ejecutivo Comercial (Ventas)** - Rol: Ventas

**Actores Secundarios:**
- **Gerente General** - Rol: Administrador
- **Cliente** - Entidad externa que recibe la factura

### 5.2 Descripción de Actores

| **ACTOR** | **ROL EN EL SISTEMA** | **DESCRIPCIÓN** |
|-----------|----------------------|-----------------|
| **Ejecutivo Comercial** | Ventas | Personal encargado de atender las solicitudes comerciales de los clientes. Puede crear, visualizar y editar facturas. Calcula el monto total de la venta aplicando el IVA según normativa. |
| **Gerente General** | Administrador | Tiene permisos completos sobre el sistema. Puede crear, visualizar, editar, anular y eliminar facturas. Supervisa todas las operaciones comerciales. |
| **Cliente** | Cliente | Persona natural o jurídica que adquiere productos o servicios de TECNOPLUS. Debe estar registrado y activo en el sistema para recibir facturación. |

---

## 6. PRECONDICIONES

Para que este caso de uso pueda ejecutarse, deben cumplirse las siguientes condiciones:

**PC-01:** El usuario (Ventas o Administrador) debe estar autenticado en el sistema con credenciales válidas.

**PC-02:** El usuario debe tener rol activo con permisos para crear facturas (Ventas o Administrador).

**PC-03:** El cliente debe estar previamente registrado en el sistema con información completa (RUC/CI, nombres, dirección, correo, teléfono).

**PC-04:** El cliente debe tener estado "Activo" en el sistema.

**PC-05:** Los productos a facturar deben estar registrados en el catálogo de productos del sistema.

**PC-06:** Los productos deben tener estado "Activo" en el sistema.

**PC-07:** Debe existir stock disponible suficiente para los productos solicitados en la factura.

**PC-08:** El sistema debe tener configurada la tasa de IVA vigente según normativa del SRI (actualmente 15%).

---

## 7. FLUJO DE EVENTOS

### 7.1 Flujo Principal (Básico)

**Escenario: Creación exitosa de una factura**

1. El caso de uso inicia cuando el Ejecutivo Comercial o Administrador selecciona la opción "Crear Nueva Factura" en el módulo de Facturación.

2. El sistema solicita seleccionar el cliente que recibirá la factura.

3. El actor busca y selecciona el cliente mediante RUC/CI o nombre desde una lista de clientes activos.

4. El sistema valida que el cliente esté en estado "Activo" y carga sus datos (RUC/CI, nombres completos, dirección, correo electrónico, teléfono).

5. El sistema presenta el formulario de creación de factura con los datos del cliente precargados y solicita agregar productos.

6. El actor selecciona "Agregar Producto" del catálogo disponible.

7. El sistema muestra el catálogo de productos activos con stock disponible.

8. El actor selecciona un producto de la lista y especifica la cantidad requerida.

9. El sistema valida que la cantidad solicitada no exceda el stock disponible del producto.

10. El sistema agrega el producto al detalle de la factura mostrando: nombre del producto, cantidad, precio unitario y subtotal de la línea.

11. El actor repite los pasos 6 a 10 para agregar todos los productos requeridos para la factura.

12. El sistema calcula automáticamente:
    - **Subtotal:** Suma de todos los subtotales de líneas de productos
    - **IVA (15%):** Subtotal × 0.15
    - **Total:** Subtotal + IVA

13. El actor opcionalmente ingresa observaciones adicionales en el campo de notas de la factura.

14. El actor selecciona "Guardar Factura".

15. El sistema valida todos los datos ingresados (cliente activo, productos válidos, cantidades permitidas).

16. El sistema genera una numeración secuencial automática e irrepetible para la factura (formato: FACT-XXXXXX).

17. El sistema registra la factura en la base de datos con estado "Pendiente de Pago".

18. El sistema genera automáticamente el documento PDF de la factura incluyendo:
    - Datos de TECNOPLUS (razón social, RUC, dirección)
    - Número de factura
    - Fecha de emisión
    - Datos del cliente
    - Detalle de productos (descripción, cantidad, precio unitario, subtotal)
    - Subtotal, IVA (15%), Total
    - Observaciones (si las hay)

19. El sistema muestra mensaje de confirmación: "Factura FACT-XXXXXX creada exitosamente" y presenta opciones para:
    - Descargar PDF
    - Enviar por correo electrónico al cliente
    - Ver detalle de la factura
    - Crear nueva factura

20. El caso de uso finaliza exitosamente.

---

### 7.2 Sub-Flujos

**No se identifican sub-flujos complejos que requieran documentación separada para este caso de uso.**

---

### 7.3 Flujos Alternos

#### **FA-01: Cliente Inactivo**

**Punto de Activación:** Paso 4 del Flujo Principal.

**Condición:** El cliente seleccionado tiene estado "Inactivo" en el sistema.

**Flujo:**

1. El sistema detecta que el cliente seleccionado está inactivo.

2. El sistema muestra mensaje de error: "No se puede crear factura. El cliente seleccionado está inactivo. Por favor, contacte al Administrador para activar el cliente."

3. El sistema no permite continuar con la creación de la factura.

4. El actor puede:
   - Seleccionar otro cliente activo (retorna al paso 3 del Flujo Principal)
   - Cancelar la operación (finaliza el caso de uso)

**Poscondición:** No se crea la factura. El sistema permanece en el formulario de selección de cliente.

---

#### **FA-02: Producto sin Stock Suficiente**

**Punto de Activación:** Paso 9 del Flujo Principal.

**Condición:** La cantidad solicitada del producto excede el stock disponible en inventario.

**Flujo:**

1. El sistema detecta que la cantidad solicitada (ejemplo: 50 unidades) excede el stock disponible (ejemplo: 30 unidades).

2. El sistema muestra mensaje de advertencia: "Stock insuficiente. Producto: [Nombre del Producto]. Stock disponible: [XX] unidades. Cantidad solicitada: [YY] unidades."

3. El sistema no agrega el producto al detalle de la factura.

4. El actor tiene las siguientes opciones:
   - **Opción A:** Modificar la cantidad solicitada ajustándola al stock disponible
   - **Opción B:** Seleccionar otro producto diferente
   - **Opción C:** Cancelar el agregado de este producto

5. Si el actor selecciona **Opción A:** modifica la cantidad y el flujo retorna al paso 9 del Flujo Principal para validación.

6. Si el actor selecciona **Opción B:** el flujo retorna al paso 7 del Flujo Principal.

7. Si el actor selecciona **Opción C:** el sistema elimina el intento de agregado y el actor puede continuar con otros productos o finalizar la factura con los productos ya agregados.

**Poscondición:** El producto con stock insuficiente no se agrega a la factura. El actor debe tomar una decisión antes de continuar.

---

#### **FA-03: Producto Inactivo**

**Punto de Activación:** Paso 8 del Flujo Principal.

**Condición:** El producto seleccionado tiene estado "Inactivo" en el catálogo.

**Flujo:**

1. El sistema detecta que el producto seleccionado está inactivo.

2. El sistema muestra mensaje de error: "El producto seleccionado no está disponible. Estado: Inactivo."

3. El sistema no permite agregar el producto al detalle de la factura.

4. El actor debe seleccionar otro producto activo.

5. El flujo retorna al paso 7 del Flujo Principal.

**Poscondición:** El producto inactivo no se agrega a la factura.

---

#### **FA-04: Factura sin Productos**

**Punto de Activación:** Paso 14 del Flujo Principal.

**Condición:** El actor intenta guardar una factura sin haber agregado ningún producto.

**Flujo:**

1. El actor selecciona "Guardar Factura" sin haber agregado productos al detalle.

2. El sistema valida el contenido de la factura y detecta que no hay productos agregados.

3. El sistema muestra mensaje de error: "No se puede guardar la factura. Debe agregar al menos un producto al detalle."

4. El sistema no permite continuar con el guardado.

5. El actor debe agregar al menos un producto antes de poder guardar la factura.

6. El flujo retorna al paso 6 del Flujo Principal.

**Poscondición:** La factura no se guarda. El formulario permanece abierto para agregar productos.

---

#### **FA-05: Error en Generación de PDF**

**Punto de Activación:** Paso 18 del Flujo Principal.

**Condición:** Ocurre un error técnico durante la generación del documento PDF.

**Flujo:**

1. El sistema intenta generar el PDF de la factura pero ocurre un error técnico (falta de recursos, error de librería, etc.).

2. El sistema registra el error en el log del sistema para auditoría técnica.

3. El sistema muestra mensaje de advertencia: "Factura FACT-XXXXXX creada exitosamente, pero hubo un error al generar el PDF. Por favor, intente regenerar el documento desde el detalle de la factura."

4. La factura queda registrada en el sistema con todos sus datos, pero sin archivo PDF asociado.

5. El sistema presenta las opciones:
   - Intentar regenerar PDF
   - Ver detalle de factura (donde podrá regenerar el PDF posteriormente)
   - Crear nueva factura

6. El caso de uso finaliza con advertencia.

**Poscondición:** La factura existe en el sistema pero sin documento PDF. El usuario debe regenerar manualmente el PDF posteriormente.

---

#### **FA-06: Cancelar Creación de Factura**

**Punto de Activación:** Cualquier momento antes del paso 14 del Flujo Principal.

**Condición:** El actor decide cancelar la creación de la factura.

**Flujo:**

1. El actor selecciona el botón "Cancelar" en cualquier momento del proceso de creación.

2. El sistema muestra mensaje de confirmación: "¿Está seguro que desea cancelar la creación de la factura? Los datos ingresados no se guardarán."

3. El actor confirma la cancelación.

4. El sistema descarta todos los datos ingresados en el formulario.

5. El sistema retorna al listado de facturas o al menú principal del módulo de facturación.

6. El caso de uso finaliza sin crear la factura.

**Poscondición:** No se crea ninguna factura. Los datos ingresados se pierden.

---

#### **FA-07: Cliente No Registrado**

**Punto de Activación:** Paso 3 del Flujo Principal.

**Condición:** El cliente deseado no existe en el sistema.

**Flujo:**

1. El actor busca al cliente por RUC/CI o nombre pero no lo encuentra en los resultados.

2. El sistema muestra mensaje: "Cliente no encontrado. ¿Desea registrar un nuevo cliente?"

3. El actor selecciona "Sí, registrar nuevo cliente".

4. El sistema redirige al módulo de "Gestión de Clientes" manteniendo el contexto de creación de factura.

5. El actor completa el registro del nuevo cliente (ejecuta el caso de uso CU-XXX: Registrar Cliente).

6. Una vez registrado exitosamente el cliente, el sistema retorna automáticamente al formulario de creación de factura con el nuevo cliente preseleccionado.

7. El flujo continúa desde el paso 5 del Flujo Principal.

**Poscondición:** Se crea un nuevo cliente en el sistema y se continúa con la creación de la factura.

---

## 8. REGLAS DEL NEGOCIO

Este caso de uso se sustenta en las siguientes reglas de negocio y normativas:

### 8.1 Reglas Funcionales

**RN-01: Numeración Secuencial Automática**
- Todas las facturas deben generarse con numeración secuencial automática e irrepetible.
- El formato de numeración es: FACT-XXXXXX (donde XXXXXX es un número secuencial de 6 dígitos).
- El sistema garantiza la unicidad de cada número de factura mediante control de concurrencia en base de datos.

**RN-02: Cálculo Automático del IVA**
- El sistema debe calcular automáticamente el IVA según la tasa vigente definida por el SRI.
- Tasa actual: 15% sobre el subtotal de productos gravados.
- Fórmula: IVA = Subtotal × 0.15
- Fórmula: Total = Subtotal + IVA

**RN-03: Estado de Cliente Activo**
- Solo se pueden emitir facturas a clientes con estado "Activo" en el sistema.
- Clientes inactivos no pueden recibir nueva facturación hasta su reactivación por el Administrador.

**RN-04: Productos Activos y con Stock**
- Solo se pueden facturar productos que cumplan simultáneamente:
  - Estado "Activo" en el catálogo
  - Stock disponible mayor o igual a la cantidad solicitada
- La validación se realiza en tiempo real al momento de agregar productos.

**RN-05: Permisos por Rol**
- **Administrador:** Puede crear, visualizar, editar, anular y eliminar facturas.
- **Ventas:** Puede crear, visualizar y editar facturas. No puede eliminar facturas.
- **Otros roles:** No tienen acceso al módulo de creación de facturas.

**RN-06: Estado Inicial de Factura**
- Toda factura recién creada se registra con estado "Pendiente de Pago".
- El estado solo cambia cuando se registran y validan pagos asociados.

**RN-07: Generación de Documento PDF**
- Toda factura debe generar automáticamente un documento PDF que cumple con los requisitos del SRI.
- El PDF debe incluir todos los datos fiscales requeridos para comprobantes electrónicos.

**RN-08: Factura Anulada No Modificable**
- Una factura en estado "Anulada" no puede ser modificada, ni se pueden registrar pagos contra ella.
- La anulación es una operación irreversible que debe ser autorizada.

**RN-09: Integridad de Datos del Cliente**
- El cliente debe tener información completa antes de facturación:
  - RUC o Cédula de Identidad válida
  - Nombres completos
  - Dirección
  - Correo electrónico
  - Teléfono de contacto

**RN-10: Auditoría de Operaciones**
- Toda creación de factura debe quedar registrada en el log de auditoría del sistema incluyendo:
  - Usuario que la creó
  - Fecha y hora exacta de creación
  - Datos completos de la transacción

### 8.2 Normativa Legal Aplicable

**RN-11: Cumplimiento SRI - Facturación Electrónica**
- Base Legal: Resoluciones del Servicio de Rentas Internas (SRI) - Ecuador 2025
- Todas las facturas deben cumplir con los requisitos de facturación electrónica establecidos por el SRI.
- Referencia: Ley de Régimen Tributario Interno

**RN-12: Obligaciones del Comerciante**
- Base Legal: Código de Comercio de Ecuador (2019), Artículos 1, 3, 4, 166-203
- Obligación de emitir comprobantes válidos por toda transacción comercial.
- Deber de mantener documentación completa y ordenada de las operaciones.

**RN-13: Derechos del Consumidor**
- Base Legal: Ley Orgánica de Defensa del Consumidor (2000), Artículos 4, 5, 17, 18
- Todo cliente tiene derecho a recibir información clara y veraz sobre productos y precios.
- Obligación de entregar comprobante de pago por toda transacción.

---

## 9. POSCONDICIONES

Al finalizar exitosamente este caso de uso, el sistema alcanza los siguientes estados:

### 9.1 Poscondiciones de Éxito (Flujo Principal Completo)

**POST-01:** Se ha creado un nuevo registro de factura en la base de datos del sistema con un identificador único.

**POST-02:** La factura tiene asignado un número secuencial único e irrepetible (formato FACT-XXXXXX).

**POST-03:** La factura se encuentra en estado "Pendiente de Pago".

**POST-04:** Se ha registrado el detalle completo de productos incluidos en la factura con sus cantidades, precios y subtotales.

**POST-05:** Se han calculado y almacenado correctamente el subtotal, IVA (15%) y total de la factura.

**POST-06:** Se ha generado y almacenado el documento PDF de la factura con toda la información fiscal requerida.

**POST-07:** Se ha registrado en el log de auditoría:
- Usuario que creó la factura
- Fecha y hora exacta de creación
- Cliente asociado
- Monto total de la factura

**POST-08:** El documento PDF está disponible para descarga inmediata y envío por correo electrónico al cliente.

**POST-09:** La factura es visible en el listado de facturas del sistema para usuarios con permisos de consulta.

**POST-10:** El sistema está listo para registrar pagos contra esta factura.

### 9.2 Poscondiciones de Fallo (Flujos Alternos)

**POST-F01:** Si se cancela la operación (FA-06), no se crea ninguna factura y el sistema retorna al estado previo.

**POST-F02:** Si hay error en generación de PDF (FA-05), la factura existe en base de datos pero sin documento PDF asociado, requiriendo regeneración manual.

**POST-F03:** Si el cliente está inactivo (FA-01), no se permite crear la factura y se mantiene el formulario para selección de otro cliente.

**POST-F04:** Si hay productos sin stock (FA-02), la factura no se completa hasta resolver la disponibilidad de productos.

---

## 10. REQUERIMIENTOS ESPECIALES

Este caso de uso tiene los siguientes requerimientos no funcionales específicos:

### 10.1 Seguridad

**RE-SEG-01: Autenticación Obligatoria**
- Todo usuario debe estar autenticado mediante tokens Sanctum (para API) o sesiones Laravel (para dashboard web).
- No se permite acceso anónimo al módulo de facturación.

**RE-SEG-02: Control de Permisos por Rol**
- El sistema debe verificar que el usuario tenga permisos de creación de facturas antes de permitir el acceso.
- Solo roles "Administrador" y "Ventas" pueden crear facturas.

**RE-SEG-03: Validación de Estado de Usuario**
- El sistema debe validar que el usuario esté en estado "Activo" antes de permitir cualquier operación.

**RE-SEG-04: Aislamiento de Datos por Cliente**
- En caso de acceso vía API por parte de clientes, estos solo deben poder consultar sus propias facturas.

### 10.2 Rendimiento y Escalabilidad

**RE-PER-01: Tiempo de Respuesta**
- La carga del formulario de creación de factura debe completarse en menos de 1 segundo.
- La búsqueda de clientes debe retornar resultados en menos de 0.5 segundos.

**RE-PER-02: Cálculos en Tiempo Real**
- Los cálculos de subtotales, IVA y total deben actualizarse instantáneamente al agregar o modificar productos.

**RE-PER-03: Generación de PDF**
- La generación del documento PDF debe completarse en menos de 3 segundos.

### 10.3 Usabilidad

**RE-USA-01: Mensajes Claros de Validación**
- Todo error de validación debe mostrar un mensaje descriptivo que indique claramente el problema y la solución.
- Ejemplo: "Stock insuficiente. Producto: Mouse Inalámbrico. Disponible: 5 unidades. Solicitado: 10 unidades."

**RE-USA-02: Confirmaciones para Operaciones Críticas**
- La cancelación de una factura en proceso de creación debe solicitar confirmación del usuario.

**RE-USA-03: Interfaz Intuitiva**
- El formulario debe presentar los campos de forma clara y organizada.
- Debe incluir ayudas visuales (placeholders, tooltips) para facilitar el llenado.

### 10.4 Confiabilidad

**RE-CON-01: Manejo de Errores**
- El sistema debe manejar todos los errores de forma controlada sin exponer información técnica sensible al usuario.
- Los errores técnicos deben registrarse en logs para análisis posterior.

**RE-CON-02: Transaccionalidad**
- La creación de una factura debe ser una operación atómica: o se completa toda la transacción o se revierte completamente.

**RE-CON-03: Recuperación ante Fallas**
- Si falla la generación del PDF, la factura debe quedar registrada permitiendo regeneración posterior.

### 10.5 Interoperabilidad

**RE-INT-01: Formato de Respuesta API**
- Las respuestas de la API deben seguir el estándar JSON.
- Códigos HTTP estándar (201 Created para éxito, 400 Bad Request para errores de validación, etc.).

**RE-INT-02: Compatibilidad de PDF**
- El PDF generado debe ser compatible con Adobe Reader y visores estándar de PDF.

### 10.6 Mantenibilidad

**RE-MAN-01: Logging Completo**
- Se debe registrar auditoría completa de cada factura creada incluyendo:
  - Usuario creador
  - Timestamp exacto
  - Cliente asociado
  - Productos y cantidades
  - Montos calculados

**RE-MAN-02: Trazabilidad**
- Cada factura debe tener un identificador único que permita su rastreo completo en el sistema.

---

## 11. RELACIONES CON OTROS CASOS DE USO

| **CASO DE USO RELACIONADO** | **TIPO DE RELACIÓN** | **OBLIGATORIEDAD** | **DESCRIPCIÓN** |
|----------------------------|---------------------|-------------------|-----------------|
| CU-002: Registrar Pago | **Secuencial** | Opcional | Después de crear una factura, el cliente puede registrar pagos contra ella. |
| CU-003: Validar Pago | **Secuencial** | Opcional | Los pagos registrados contra facturas creadas deben ser validados. |
| CU-XXX: Registrar Cliente | **Include/Extend** | Opcional | Si el cliente no existe, se puede crear durante el proceso de facturación (FA-07). |
| CU-XXX: Consultar Productos | **Include** | Implícito | La creación de factura requiere consultar el catálogo de productos y stock. |
| CU-XXX: Anular Factura | **Secuencial** | Opcional | Una factura creada puede ser anulada posteriormente si es necesario. |

---

## 12. MODELAMIENTO

### 12.1 Diagrama de Caso de Uso (UML)

```
                                   ┌─────────────────────────────────────┐
                                   │  Sistema de Facturación TECNOPLUS   │
                                   │                                     │
    ┌──────────────────┐          │     ┌─────────────────────────┐     │
    │                  │          │     │                         │     │
    │   Ejecutivo      │──────────┼────▶│    Crear Factura       │     │
    │   Comercial      │          │     │      (CU-001)          │     │
    │   (Ventas)       │          │     │                         │     │
    │                  │          │     └────────┬────────────────┘     │
    └──────────────────┘          │              │                      │
                                   │              │ <<include>>          │
    ┌──────────────────┐          │              │                      │
    │                  │          │              ▼                      │
    │   Gerente        │──────────┼────▶ ┌─────────────────────────┐   │
    │   General        │          │      │  Consultar Catálogo     │   │
    │ (Administrador)  │          │      │  de Productos           │   │
    │                  │          │      └─────────────────────────┘   │
    └──────────────────┘          │                                     │
                                   │              │ <<include>>          │
          ┌───────────┐           │              │                      │
          │           │           │              ▼                      │
          │  Cliente  │◀──────────┼───── ┌─────────────────────────┐   │
          │           │  Recibe   │      │   Generar PDF Factura   │   │
          └───────────┘  PDF      │      └─────────────────────────┘   │
                                   │                                     │
                                   │              │ <<extend>>           │
                                   │              │                      │
                                   │              ▼                      │
                                   │      ┌─────────────────────────┐   │
                                   │      │   Registrar Cliente     │   │
                                   │      │   (Si no existe)        │   │
                                   │      └─────────────────────────┘   │
                                   │                                     │
                                   └─────────────────────────────────────┘
```

**Descripción del Diagrama:**
- **Actores Principales:** Ejecutivo Comercial (Ventas) y Gerente General (Administrador) pueden crear facturas.
- **Actor Secundario:** Cliente recibe la factura en PDF.
- **Include:** La creación de factura incluye obligatoriamente consultar el catálogo de productos y generar PDF.
- **Extend:** Opcionalmente puede extenderse a registrar un nuevo cliente si este no existe.

---

### 12.2 Diagrama de Actividades

```
                            CREAR FACTURA - Diagrama de Actividades

┌─────────── VENTAS/ADMINISTRADOR ────────────┐┌──────── SISTEMA ──────────┐
│                                              ││                            │
│  [Inicio]                                    ││                            │
│     │                                        ││                            │
│     ▼                                        ││                            │
│ ┌────────────────────────┐                  ││                            │
│ │ Seleccionar "Crear     │                  ││                            │
│ │ Nueva Factura"         │                  ││                            │
│ └───────────┬────────────┘                  ││                            │
│             │                                ││                            │
│             ▼                                ││   ┌─────────────────────┐ │
│ ┌────────────────────────┐                  ││   │ Mostrar formulario  │ │
│ │ Buscar y seleccionar   │◀─────────────────┼┼───│ de búsqueda de      │ │
│ │ cliente                │                  ││   │ clientes            │ │
│ └───────────┬────────────┘                  ││   └─────────────────────┘ │
│             │                                ││             │              │
│             │                                ││             ▼              │
│             │                                ││   ┌─────────────────────┐ │
│             │                                ││   │ Validar estado      │ │
│             │                                ││   │ del cliente         │ │
│             │                                ││   └──────────┬──────────┘ │
│             │                                ││              │             │
│             │                                ││        ┌─────┴─────┐      │
│             │                                ││        │ ¿Activo?  │      │
│             │                                ││        └─────┬─────┘      │
│             │                                ││              │             │
│             │                                ││        No    │    Sí      │
│             │                                ││   ┌──────────▼────────┐   │
│             │                                ││   │ Mostrar error:    │   │
│             │                                ││   │ Cliente Inactivo  │   │
│             │                                ││   └───────────────────┘   │
│             │                                ││              │             │
│             ▼                                ││             Sí             │
│ ┌────────────────────────┐                  ││              │             │
│ │ Agregar productos      │◀─────────────────┼┼──────────────┘             │
│ │ al detalle             │                  ││   ┌─────────────────────┐ │
│ └───────────┬────────────┘                  ││   │ Cargar catálogo de  │ │
│             │                                ││   │ productos activos   │ │
│             ▼                                ││   └─────────────────────┘ │
│ ┌────────────────────────┐                  ││             │              │
│ │ Seleccionar producto   │                  ││             ▼              │
│ │ y cantidad             │                  ││   ┌─────────────────────┐ │
│ └───────────┬────────────┘                  ││   │ Validar stock       │ │
│             │                                ││   │ disponible          │ │
│             │                                ││   └──────────┬──────────┘ │
│             │                                ││              │             │
│             │                                ││        ┌─────┴──────┐     │
│             │                                ││        │ ¿Hay stock?│     │
│             │                                ││        └─────┬──────┘     │
│             │                                ││              │             │
│             │                                ││       No     │    Sí      │
│             │                                ││   ┌──────────▼────────┐   │
│             │                                ││   │ Mostrar error:    │   │
│             │                                ││   │ Stock insuficiente│   │
│             │                                ││   └──────────┬────────┘   │
│             │                                ││              │             │
│             ▼                                ││             Sí             │
│      ┌──────────────┐                       ││              │             │
│      │ ¿Agregar más?│                       ││              ▼             │
│      └──────┬───────┘                       ││   ┌─────────────────────┐ │
│             │                                ││   │ Agregar producto    │ │
│       Sí    │    No                         ││   │ al detalle          │ │
│      ┌──────┘                               ││   └─────────────────────┘ │
│      │      │                               ││              │             │
│      └──────┘                               ││              │             │
│             │                                ││              ▼             │
│             ▼                                ││   ┌─────────────────────┐ │
│ ┌────────────────────────┐                  ││   │ Calcular subtotal,  │ │
│ │ Ingresar observaciones │                  ││   │ IVA (15%) y total   │ │
│ │ (opcional)             │                  ││   └─────────────────────┘ │
│ └───────────┬────────────┘                  ││              │             │
│             │                                ││              ▼             │
│             ▼                                ││   ┌─────────────────────┐ │
│ ┌────────────────────────┐                  ││   │ Mostrar resumen     │ │
│ │ Seleccionar "Guardar   │                  ││   │ de factura          │ │
│ │ Factura"               │                  ││   └─────────────────────┘ │
│ └───────────┬────────────┘                  ││              │             │
│             │                                ││              ▼             │
│             │                                ││   ┌─────────────────────┐ │
│             │                                ││   │ Validar datos       │ │
│             │                                ││   │ completos           │ │
│             │                                ││   └──────────┬──────────┘ │
│             │                                ││              │             │
│             │                                ││        ┌─────┴─────┐      │
│             │                                ││        │ ¿Válidos? │      │
│             │                                ││        └─────┬─────┘      │
│             │                                ││              │             │
│             │                                ││       No     │    Sí      │
│             │                                ││   ┌──────────▼────────┐   │
│             │                                ││   │ Mostrar errores   │   │
│             │                                ││   │ de validación     │   │
│             │                                ││   └───────────────────┘   │
│             │                                ││              │             │
│             ▼                                ││             Sí             │
│ ┌────────────────────────┐                  ││              │             │
│ │ Confirmar creación     │                  ││              ▼             │
│ └───────────┬────────────┘                  ││   ┌─────────────────────┐ │
│             │                                ││   │ Generar número      │ │
│             │                                ││   │ secuencial FACT-XXX │ │
│             │                                ││   └──────────┬──────────┘ │
│             │                                ││              │             │
│             │                                ││              ▼             │
│             │                                ││   ┌─────────────────────┐ │
│             │                                ││   │ Guardar factura en  │ │
│             │                                ││   │ base de datos       │ │
│             │                                ││   │ Estado: Pendiente   │ │
│             │                                ││   └──────────┬──────────┘ │
│             │                                ││              │             │
│             │                                ││              ▼             │
│             │                                ││   ┌─────────────────────┐ │
│             │                                ││   │ Generar documento   │ │
│             │                                ││   │ PDF de factura      │ │
│             │                                ││   └──────────┬──────────┘ │
│             │                                ││              │             │
│             │                                ││              ▼             │
│             │                                ││   ┌─────────────────────┐ │
│             │                                ││   │ Registrar en log    │ │
│             │                                ││   │ de auditoría        │ │
│             │                                ││   └──────────┬──────────┘ │
│             │                                ││              │             │
│             ▼                                ││              ▼             │
│ ┌────────────────────────┐                  ││   ┌─────────────────────┐ │
│ │ Recibir confirmación   │◀─────────────────┼┼───│ Mostrar mensaje:    │ │
│ │ de factura creada      │                  ││   │ "Factura creada     │ │
│ └───────────┬────────────┘                  ││   │ exitosamente"       │ │
│             │                                ││   └─────────────────────┘ │
│             ▼                                ││                            │
│ ┌────────────────────────┐                  ││                            │
│ │ Descargar PDF /        │                  ││                            │
│ │ Enviar por correo      │                  ││                            │
│ └───────────┬────────────┘                  ││                            │
│             │                                ││                            │
│             ▼                                ││                            │
│          [Fin]                               ││                            │
│                                              ││                            │
└──────────────────────────────────────────────┘└────────────────────────────┘
```

**Descripción del Diagrama:**
- El diagrama muestra el flujo completo desde la selección de cliente hasta la generación del PDF.
- Incluye las principales decisiones: validación de cliente activo, validación de stock, y validación de datos completos.
- Los procesos de validación y cálculos son ejecutados por el sistema automáticamente.
- El usuario (Ventas/Administrador) interactúa principalmente para ingresar datos y tomar decisiones.

---

## 13. ANEXOS

### 13.1 Anexo A: Mockup de Interfaz de Creación de Factura

*(Aquí se incluiría un mockup visual de la interfaz de usuario mostrando el formulario de creación de factura)*

**Elementos visuales sugeridos:**
- Selector de cliente con búsqueda autocompletable
- Tabla dinámica para agregar productos
- Campos calculados automáticamente: Subtotal, IVA, Total
- Botones: "Agregar Producto", "Guardar Factura", "Cancelar"
- Campo de observaciones opcional

---

### 13.2 Anexo B: Ejemplo de Factura PDF Generada

**Estructura del PDF:**

```
─────────────────────────────────────────────────────────────
                        TECNOPLUS S.A.
              RUC: 1234567890001
         Dirección: Av. Principal 123, Quito, Ecuador
              Teléfono: (02) 123-4567
            Email: facturacion@tecnoplus.com

─────────────────────────────────────────────────────────────
                  FACTURA ELECTRÓNICA
─────────────────────────────────────────────────────────────

Factura No:     FACT-000123
Fecha Emisión:  28/10/2025
Estado:         Pendiente de Pago

DATOS DEL CLIENTE:
RUC/CI:         1723456789001
Cliente:        Empresa XYZ S.A.
Dirección:      Calle Falsa 456, Quito
Teléfono:       099-123-4567
Email:          cliente@empresaxyz.com

─────────────────────────────────────────────────────────────
DETALLE DE PRODUCTOS:
─────────────────────────────────────────────────────────────
Cant.  Descripción               P.Unit    Subtotal
─────────────────────────────────────────────────────────────
  5    Mouse Inalámbrico         $15.00    $75.00
  2    Teclado Mecánico          $85.00    $170.00
  1    Monitor LED 24"           $250.00   $250.00
─────────────────────────────────────────────────────────────
                           Subtotal:       $495.00
                           IVA (15%):      $74.25
─────────────────────────────────────────────────────────────
                           TOTAL:          $569.25
─────────────────────────────────────────────────────────────

Observaciones:
Entrega programada para el 30/10/2025

Forma de Pago: Transferencia Bancaria
Plazo de Pago: 15 días

─────────────────────────────────────────────────────────────
Este documento constituye un comprobante válido de venta
según las regulaciones del SRI de Ecuador.
─────────────────────────────────────────────────────────────
```

---

### 13.3 Anexo C: Referencia Normativa Legal Detallada

**1. Servicio de Rentas Internas (SRI) - Ecuador**
- **Tema:** Facturación Electrónica
- **Año:** 2025
- **Descripción:** Regulaciones vigentes sobre emisión de comprobantes electrónicos, requisitos de información fiscal, y procedimientos de autorización.
- **URL Referencia:** [Facturación Electrónica SRI](https://www.sri.gob.ec)

**2. Código de Comercio de Ecuador**
- **Publicación:** Suplemento del Registro Oficial No. 497, 29/05/2019
- **Artículos Aplicables:** Art. 1, 3, 4, 166-203
- **Tema:** Obligaciones de los comerciantes, registro de operaciones, emisión de comprobantes.
- **Entidad:** Superintendencia de Compañías, Valores y Seguros

**3. Ley Orgánica de Defensa del Consumidor**
- **Publicación:** Registro Oficial Suplemento No. 116, 10/07/2000
- **Artículos Aplicables:** Art. 4, 5, 17, 18
- **Tema:** Derechos del consumidor, obligación de entregar información clara y comprobantes de pago.
- **Entidad:** Defensoría del Pueblo del Ecuador

---

### 13.4 Anexo D: Glosario de Términos Técnicos

| **TÉRMINO** | **DEFINICIÓN** |
|------------|---------------|
| **API REST** | Interfaz de programación de aplicaciones que sigue el estilo arquitectónico REST para comunicación entre sistemas mediante HTTP. |
| **Audit Log** | Registro cronológico de eventos y acciones realizadas en el sistema para fines de auditoría y trazabilidad. |
| **Dashboard** | Panel de control visual que presenta información resumida y métricas importantes del sistema. |
| **Laravel Sanctum** | Sistema de autenticación de tokens para aplicaciones SPA (Single Page Applications) y APIs en Laravel. |
| **Paginación** | Técnica de dividir grandes conjuntos de datos en páginas más pequeñas para mejorar rendimiento y usabilidad. |
| **PDF** | Portable Document Format - Formato estándar de documentos electrónicos que preserva el formato original. |
| **Token de Autenticación** | Credencial digital que identifica y autentica a un usuario en el sistema sin necesidad de enviar contraseñas repetidamente. |
| **Transacción Atómica** | Operación de base de datos que se ejecuta completamente o se revierte completamente, sin estados intermedios. |
| **Validación en Tiempo Real** | Proceso de verificar datos inmediatamente mientras el usuario los ingresa, sin necesidad de enviar un formulario. |

---

## FIN DEL DOCUMENTO

**Versión:** 1.0.0  
**Fecha de Emisión:** 28/10/2025  
**Próxima Revisión:** 28/01/2026

---

**Confidencial** © 2025 Dirección de Tecnología - TECNOPLUS  
Este documento contiene información confidencial y es propiedad de TECNOPLUS.  
Prohibida su reproducción total o parcial sin autorización expresa.