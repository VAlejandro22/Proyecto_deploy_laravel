# Especificación de Requerimientos del Sistema (SRS)

Proyecto: Sistema de Facturación y Pagos TECNOPLUS  
Versión: 1.0  
Fecha: 2025-10-27

## 1. Introducción

### 1.1 Propósito
Este documento especifica los requerimientos funcionales y no funcionales del Sistema de Facturación y Pagos de TECNOPLUS. Sirve como contrato entre los interesados del negocio y el equipo técnico para el diseño, desarrollo, pruebas y mantenimiento del sistema.

### 1.2 Alcance
El sistema permite gestionar usuarios y roles, clientes, productos, facturas, pagos de clientes y tokens de acceso a la API. Incluye un dashboard web para operadores internos según rol y una API REST autenticada con tokens para integración de clientes.

### 1.3 Definiciones, acrónimos y abreviaturas
- API: Interfaz de Programación de Aplicaciones (REST/JSON)
- Token: Credencial de acceso emitida por el sistema para uso de la API (Sanctum)
- RF: Requerimiento Funcional
- RNF: Requerimiento No Funcional
- PDF: Documento en formato Portable Document Format
- Roles estándar: Administrador, Ventas, Secretario, Bodega, Pagos, Cliente

### 1.4 Referencias
- Archivo `API_COMPLETE_DOCUMENTATION.md` (endpoints y ejemplos)
- Archivo `DOCUMENTACION_PAGOS.md` (flujos de pago)
- Archivo `IMPLEMENTACION_COMPLETA.md` (resumen de funcionalidades)
- Rutas: `routes/api.php`, `routes/web.php`

### 1.5 Visión General
El sistema centraliza la facturación y el registro/validación de pagos de clientes. Los usuarios internos operan desde el dashboard con permisos por rol. Los clientes consumen su propia información a través de la API segura.

## 2. Actores y perfiles
- Administrador: Gestión integral (usuarios, roles, tokens, clientes, productos, facturas). Puede eliminar.
- Ventas: Crea/edita/anula facturas; consulta clientes y productos activos.
- Secretario: Gestiona clientes; consulta facturas.
- Bodega: Gestiona productos y stock; consulta productos activos.
- Pagos: Valida pagos ingresados por clientes (aprobar/rechazar) y consulta historial.
- Cliente: Accede vía API para consultar sus facturas, estadísticas y registrar pagos propios.

## 3. Suposiciones y dependencias
- Autenticación API mediante Laravel Sanctum (Bearer Token).
- Gestión de roles y permisos con Spatie Permission.
- Base de datos relacional (MySQL/MariaDB o equivalente soportado por Laravel).
- Generación de PDF para facturas disponible desde la interfaz y API.
- Validación de pagos es un proceso manual por usuarios con rol Pagos dentro del dashboard.

## 4. Restricciones
- Acceso estrictamente controlado por rol/permiso y estado activo del usuario.
- Los clientes sólo acceden a sus propios datos (facturas y pagos).
- Eliminaciones críticas reservadas al Administrador.
- Límites de paginación y filtros aplican a listados extensos para rendimiento.

## 5. Modelo de alto nivel (entidades)
- Usuario (User) — campos de autenticación, estado activo, roles/permisos.
- Rol (Role) — catálogo de roles; asociación con usuarios.
- Cliente (Cliente) — datos de identificación y relación 1:1 con Usuario opcional.
- Producto (Producto) — catálogo con precio, stock, estado.
- Factura (Factura) — cabecera y detalles (productos, cantidades, totales, estado: pendiente/pagada/anulada).
- Pago (Pago) — registro de pago de factura con estado: pendiente/aprobado/rechazado.
- Token API — credenciales emitidas para usuarios/clientes; revocables.

Relaciones clave:
- User 1—1 Cliente (opcional)  
- Factura N—N Producto (con cantidad y precio unitario)  
- Pago N—1 Factura; Pago N—1 Cliente (vía usuario cliente)

## 6. Reglas de negocio principales
- RB-01: Un Cliente sólo puede ver/pagar sus facturas.  
- RB-02: Una Factura sólo puede pagarse si está en estado pendiente o activa.  
- RB-03: La suma de Pagos aprobados no puede exceder el total de la Factura.  
- RB-04: Al aprobar un Pago que completa el monto, la Factura cambia a estado pagada.  
- RB-05: Al rechazar un Pago, la Factura mantiene su estado previo.  
- RB-06: Sólo Administrador puede eliminar usuarios/clientes/productos/facturas.  
- RB-07: El estado del Usuario (activo/inactivo) condiciona el acceso a dashboard y API.  
- RB-08: La generación y revocación de tokens de API sólo es posible por Administrador o por el propio usuario respecto a su token actual.

## 7. Requerimientos funcionales (RF)

Nota: Cada RF incluye breve descripción y actores. Criterios de aceptación abreviados.

### 7.1 Autenticación y seguridad
- RF-001 Autenticación API con token
  - Los consumidores de API deben enviar `Authorization: Bearer {token}`.
  - Actores: Cliente, Administrador, Usuarios internos vía API.
  - CA: Peticiones sin token o con token inválido devuelven 401; con token válido y usuario activo, 200.

- RF-002 Control de acceso por rol/permiso y estado
  - El sistema aplica middlewares de rol/permiso y estado del usuario en dashboard y API.
  - Actores: Todos.
  - CA: Intentos sin permiso devuelven 403 y se registran.

### 7.2 Gestión de usuarios y roles
- RF-010 CRUD de usuarios (web y API)
  - Listar, crear, ver, editar, activar/inactivar y eliminar usuarios.  
  - Actores: Administrador.
  - CA: Validaciones de email único, contraseñas coinciden, cambios persisten.

- RF-011 Gestión de roles/permisos
  - Asignación de roles a usuarios/clientes y aplicación de permisos.
  - Actores: Administrador.
  - CA: Vistas y endpoints reflejan los permisos asignados.

### 7.3 Gestión de clientes
- RF-020 CRUD de clientes (web y API)
  - Listar, crear, ver, editar, activar/inactivar y eliminar clientes.
  - Actores: Administrador, Secretario (sin eliminar), Ventas (consulta).
  - CA: Validaciones de datos obligatorios; eliminación restringida a Administrador.

- RF-021 Relación usuario-cliente
  - Posibilidad de asociar un usuario a un cliente; creación automática cuando aplique.
  - Actores: Administrador.
  - CA: Endpoints devuelven sólo registros asociados al cliente autenticado cuando el rol es Cliente.

### 7.4 Gestión de productos
- RF-030 CRUD de productos (web y API)
  - Listar, crear, ver, editar, activar/inactivar y eliminar productos.
  - Actores: Administrador, Bodega (sin eliminar), Ventas (consulta activos).
  - CA: Validaciones de precio, stock no negativo y códigos únicos.

- RF-031 Consulta de productos activos y verificación de stock
  - Endpoints para obtener productos activos y verificar stock disponible.
  - Actores: Administrador, Bodega, Ventas.
  - CA: Respuestas incluyen disponibilidad real y mensajes claros ante insuficiencia.

### 7.5 Facturación
- RF-040 CRUD de facturas (web y API)
  - Crear, listar, ver, editar, anular y eliminar (sólo Admin) facturas.
  - Actores: Administrador, Ventas (sin eliminar), otros roles con alcances de lectura según configuración.
  - CA: Cálculo correcto de totales y estados; auditoría básica de cambios.

- RF-041 Generación de PDF de factura
  - Generar y descargar el PDF de la factura desde web/API.
  - Actores: Administrador, Ventas y otros con permiso de lectura de esa factura.
  - CA: PDF refleja datos y totales actuales de la factura.

- RF-042 Estadísticas de facturación
  - Indicadores agregados por periodo/estado para dashboard o API.
  - Actores: Según permisos del usuario autenticado.
  - CA: Respuestas incluyen métricas y respetan filtros/alcances de visibilidad.

### 7.6 Pagos
- RF-050 Registro de pagos por cliente (API)
  - El cliente registra un pago sobre una factura propia con datos: factura, tipo, número de transacción, monto y observaciones.
  - Actores: Cliente (API); Pagos (aprobación).
  - CA: Alta en estado pendiente; validaciones de pertenencia y monto.

- RF-051 Consulta de pagos del cliente (API)
  - Listar y ver pagos del cliente autenticado con filtros por estado/factura.
  - Actores: Cliente.
  - CA: Sólo datos propios; paginación y filtros funcionan.

- RF-052 Validación de pagos (web)
  - Operadores con rol Pagos consultan pendientes, aprueban o rechazan pagos.
  - Actores: Pagos, Administrador.
  - CA: Al aprobar, se actualiza el estado del pago y la factura a pagada cuando corresponda; al rechazar, el pago queda rechazado y la factura no cambia.

### 7.7 Tokens de API
- RF-060 Emisión y gestión de tokens
  - Administrador emite tokens para clientes/usuarios, lista tokens y puede revocarlos; el usuario puede revocar su token actual.
  - Actores: Administrador, Usuario autenticado (revocación propia).
  - CA: Tokens se muestran en dashboard; revocación inmediata invalida el acceso.

### 7.8 Dashboard
- RF-070 Dashboard con métricas y accesos por rol
  - Visualización de estadísticas, accesos a módulos y secciones de gestión según rol.
  - Actores: Usuarios internos.
  - CA: Tarjetas/indicadores y menús visibles acorde a permisos; acceso denegado al resto.

## 8. Requerimientos no funcionales (RNF)

### 8.1 Seguridad
- RNF-001 Autenticación: Tokens Sanctum para API; sesión para dashboard.
- RNF-002 Autorización: Control por roles/permisos; verificación de estado activo.
- RNF-003 Protección de datos: Cifrado de contraseñas (bcrypt/argon2), no exponer secretos en respuestas.
- RNF-004 Aislamiento de datos de clientes: Acceso estrictamente restringido a recursos propios.

### 8.2 Rendimiento y escalabilidad
- RNF-010 Paginación por defecto en listados; filtros por estado/fecha/búsqueda.
- RNF-011 Tiempo de respuesta objetivo < 1s en operaciones de lectura bajo carga moderada.
- RNF-012 Diseñado para escalar horizontalmente (stateless API; tokens).

### 8.3 Disponibilidad y confiabilidad
- RNF-020 Manejo de errores consistente con códigos HTTP y mensajes claros.
- RNF-021 Registro de logs de aplicación y auditoría básica de cambios críticos.

### 8.4 Usabilidad
- RNF-030 UI del dashboard clara, con secciones por módulo y documentación rápida para tokens y API.
- RNF-031 Mensajes de validación amigables y acciones confirmables en operaciones críticas.

### 8.5 Interoperabilidad
- RNF-040 API REST con JSON; encabezados estándar; soporte CORS configurable.
- RNF-041 Generación de PDF portable para facturas.

### 8.6 Mantenibilidad y calidad
- RNF-050 Arquitectura Laravel estándar (Controladores, Modelos, Rutas, Middlewares) y uso de paquetes conocidos (Sanctum, Spatie).
- RNF-051 Pruebas automatizadas (PHPUnit/Pest) y documentación actualizable en repo.
- RNF-052 Estilo de código y análisis estático (phpstan) sin errores críticos.

### 8.7 Portabilidad
- RNF-060 Compatible con entornos Windows/Linux; PHP 8.x; base de datos soportada por Laravel.

## 9. Interfaz del sistema

### 9.1 Interfaz de usuario (dashboard)
- Gestión de Usuarios (sólo Administrador) y estado activo.
- Gestión de Clientes (Administrador/Secretario) y asignación de roles a clientes.
- Gestión de Productos (Administrador/Bodega) y verificación de stock.
- Gestión de Facturas (Administrador/Ventas) con generación de PDF y anulación.
- Gestión de Pagos (Administrador/Pagos) con aprobación/rechazo e historial.
- Gestión de Tokens API (Administrador): emisión, listado y revocación.

### 9.2 Interfaz de programación (API)
Resumen de rutas clave (ver detalle en `API_COMPLETE_DOCUMENTATION.md`):
- `/api/users`, `/api/clientes`, `/api/productos`, `/api/facturas`, `/api/dashboard`.
- `/api/api-tokens`, `/api/my-token`.
- Rutas específicas de cliente: `/api/cliente/facturas`, `/api/cliente/facturas-stats`, `/api/cliente/pagos`.

## 10. Criterios de aceptación generales
- Autenticación/Autorización aplicadas consistentemente en todas las rutas.
- Filtros y paginación funcionales en listados.
- Estados de factura y pago se actualizan conforme a reglas de negocio.
- PDF de factura generado sin errores y con datos correctos.
- Tokens emitidos/revocados reflejan acceso permitido/denegado inmediatamente.

## 11. Gestión de cambios
Cualquier cambio en requerimientos debe registrarse con versión, fecha, motivo e impacto, actualizando este documento y los archivos de documentación asociados.

## 12. Trazabilidad y referencias cruzadas
- Rutas y controladores: `routes/api.php`, `routes/web.php`, `app/Http/Controllers/**`.
- Modelos: `app/Models/**`.
- Documentación complementaria: `IMPLEMENTACION_COMPLETA.md`, `DOCUMENTACION_PAGOS.md`, `API_COMPLETE_DOCUMENTATION.md`.

---
Documento generado por ingeniería inversa del repositorio actual para TECNOPLUS. 