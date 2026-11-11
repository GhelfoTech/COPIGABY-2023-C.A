# MANUAL DE USUARIO — SISTEMA COPIGABY 2023 C.A.

**Versión:** 1.0 · **Fecha:** Noviembre 2026
**Empresa:** CopiGaby 2023 C.A. · **RIF:** J-504149357

---

## 1. INTRODUCCIÓN

El **CopiGaby 2023** es un sistema desarrollado en PHP para la gestión integral de la empresa CopiGaby 2023 C.A.: control de pedidos de clientes, compras a proveedores, inventario de productos e insumos, catálogo de servicios, configuración comercial (monedas, IVA, métodos de pago, unidades de medida) y reportes administrativos.

### 1.1 Requisitos para su uso
- Navegador web moderno (Chrome, Edge, Firefox).
- Conexión al servidor local o de la empresa.
- Cuenta de usuario entregada por el administrador.

### 1.2 Roles de usuario
| Rol | Descripción | Alcance |
|-----|-------------|---------|
| **Administrador (1)** | Acceso total | Todos los módulos, incluida la Configuración y los Reportes |
| **Empleado (2)** | Acceso limitado | Pedidos, Clientes, Cashea, Productos, Categorías, Servicios y consulta de Reportes |

---

## 2. PANTALLA DE INICIO DE SESIÓN (LOGIN)

Es la puerta de entrada al sistema. Sin sesión iniciada no es posible acceder a ningún módulo.

### 2.1 Elementos de la pantalla
| Elemento | Descripción |
|----------|-------------|
| **Logo y datos de la empresa** | Muestra la identidad de CopiGaby 2023 C.A. y su RIF (J-504149357) |
| **Campo Usuario** | Escriba aquí el nombre de usuario asignado |
| **Campo Contraseña** | Clave asignada por el administrador |
| **Botón 👁 Mostrar/Ocultar** | Al pulsar el ícono de ojo, alterna entre ver la clave en texto plano (`text`) o oculta (`password`) para verificar lo escrito |
| **Botón "Ingresar al Sistema"** | Valida las credenciales contra la base de datos y accede al panel principal |

### 2.2 Pasos para iniciar sesión
1. Abra el sistema en el navegador (`?url=login`).
2. Escriba su **usuario** y **contraseña**.
3. (Opcional) Pulse el ícono del ojo para verificar la clave.
4. Pulse **"Ingresar al Sistema"**.

### 2.3 Mensajes comunes
- *Credenciales incorrectas*: revise usuario y contraseña; distingue mayúsculas.
- *Sesión expirada / no autorizado*: vuelva a iniciar sesión.
- Si olvida su contraseña, solicite al administrador su restablecimiento desde el módulo **Usuarios**.

> **Recomendación:** cierre sesión al terminar el día usando la opción **"Cerrar sesión"** del menú lateral, especialmente si comparte equipo.

---

## 3. PANEL PRINCIPAL (DASHBOARD)

Al ingresar, el sistema muestra el **Panel Principal** con el resumen de actividad del negocio.

### 3.1 Tarjetas de métricas
Ubicadas en la parte superior, se actualizan automáticamente desde la base de datos:

| Tarjeta | Qué muestra |
|---------|-------------|
| **Productos Activos** | Cantidad de productos/insumos registrados con estatus activo |
| **Servicios Ofrecidos** | Total de servicios habilitados en el catálogo |
| **Usuarios Activos** | Número de cuentas de usuario operativas |
| **Ingresos del Mes** | Sumatoria de ventas del mes en curso, expresada en dólares ($) |

### 3.2 Módulo de Tasa del Día
Sección fundamental para trabajar con precios en bolívares (Bs.) cuando la moneda activa es extranjera (USD/EUR).

**Componentes:**
- **Selector "Tipo de moneda"**: lista las monedas registradas (ej. Dólar USD $, Euro €), indicando cuál es la moneda activa del sistema.
- **Campo "Nueva tasa (Bs.)"**: monto en bolívares equivalente a **una unidad** de la moneda seleccionada. Admite decimales (ej. `120,50`).
- **Botón "GUARDAR TASA"**: registra la nueva tasa con fecha y hora automáticas.

**Tabla "Historial reciente":** lista las últimas 5 tasas registradas (moneda, fecha/hora y monto Bs.).

**Pasos para actualizar la tasa:**
1. Seleccione la **moneda** en el selector.
2. Escriba la **nueva tasa en Bs.** (solo números positivos con decimales).
3. Pulse **GUARDAR TASA**. El sistema confirmará con un mensaje de éxito y la tasa quedará aplicada para el cálculo de precios en bolívares.

> ⚠️ Es responsabilidad diaria del administrador actualizar la tasa al valor oficial del día para que los precios convertidos sean correctos.

### 3.3 Accesos rápidos
Tarjetas de atajo que llevan directamente a las tareas más frecuentes (solo aparecen si su rol tiene permiso sobre el módulo):

| Acceso | Función |
|--------|---------|
| **Gestionar pedidos** | Ir al módulo de ventas y órdenes de clientes |
| **Registrar compra** | Registrar entradas de mercancía y actualizar inventario |
| **Ver inventario** | Consultar productos e insumos disponibles |
| **Ver servicios** | Revisar el catálogo de servicios ofrecidos |

---

## 4. MENÚ LATERAL GENERAL

Banda lateral fija en el lado izquierdo. En su cabecera se muestra el logo y el nombre del sistema. Las opciones visibles dependen de su rol.

| Opción | Descripción |
|--------|-------------|
| **Inicio** | Regresa al Panel Principal (Dashboard) con métricas y tasa del día |
| **Pedido** (desplegable) | Contiene tres submódulos: **Pedidos** (registrar y consultar órdenes de venta), **Clientes** (agenda de clientes con datos de contacto) y **Cashea** (ventas a crédito con modalidad de pago por cuotas) |
| **Compras** | Registro de compras a proveedores: selecciona productos, cantidades y costos; actualiza automáticamente el inventario |
| **Producto** (desplegable) | **Productos-Insumo** (alta/baja/modificación del inventario: código, nombre, precio, existencia) y **Categoría** (clasificación de los productos) |
| **Servicios** | Catálogo de servicios que ofrece el negocio (impresiones, fotocopias, anillados, etc.), con su precio |
| **Proveedores** | Gestión de proveedores de mercancía: datos de contacto y asociación con compras |
| **Configuración** *(solo admin)* | Submenú con: **Rol** (permisos por perfil), **Método de Pago**, **Moneda** (monedas y tasas), **IVA**, **Empresa** (datos fiscales como nombre y RIF), **Unidad de Medida** y **Usuarios** (creación de cuentas) |
| **Reporte** | Generación de reportes de ventas, compras e inventario para consulta y análisis |
| **Ayuda** | Abre el Centro de Ayuda con el Manual de Usuario, para **visualizarlo en pantalla** o **descargarlo** (PDF, Word o formato base) |
| **Cerrar sesión** | Finaliza la sesión de forma segura y regresa al login |

> **Nota:** las opciones con flecha ▼ son desplegables: al pulsarlas se expanden mostrando sus submódulos. Si una opción no aparece, es porque su rol no tiene asignado ese módulo.

---

## 5. OPERACIONES POR MÓDULO (GUÍA BREVE)

### 5.1 Pedidos y Clientes
- **Nuevo pedido:** seleccione el cliente, agregue productos/servicios con sus cantidades, verifique el total y guarde. La existencia del inventario se descuenta automáticamente.
- **Clientes:** registre nombre, RIF/CI, teléfono y dirección. Los clientes pueden editarse pero generalmente no eliminarse si tienen pedidos asociados.

### 5.2 Cashea (crédito)
Registre la venta a crédito, el monto total y el plan de pagos. Consulte el saldo pendiente de cada cliente desde su listado.

### 5.3 Compras
1. Seleccione el **proveedor**.
2. Agregue los productos y cantidades compradas.
3. Confirme: el inventario **aumenta** automáticamente y queda el registro del gasto.

### 5.4 Productos y Categorías
Cree categorías primero (ej. "Papelería", "Impresión"). Luego registre cada producto con código, categoría, unidad de medida, precio y existencia inicial. Use la búsqueda del listado para ubicar registros rápido.

### 5.5 Servicios
Defina el nombre, descripción y precio del servicio. Los servicios se agregan a los pedidos igual que los productos, pero no afectan el inventario físico.

### 5.6 Configuración (Administrador)
Revise periódicamente: los **métodos de pago** activos (efectivo, Pago Móvil, transferencia), el **IVA** vigente, las **monedas**, los **usuarios** nuevos y los **roles** con sus permisos.

### 5.7 Reportes
Consulte los reportes por rango de fecha para conocer ventas, ingresos y movimiento de inventario. Puede visualizarlos en pantalla o imprimirlos/exportarlos desde el navegador.

---

## 6. CENTRO DE AYUDA (SECCIÓN AYUDA)

Desde el menú lateral, la opción **Ayuda** abre este manual:
- **Visualizar en el navegador:** se muestra el documento en pantalla dentro de la propia vista.
- **Descargar PDF / Word (.docx) / formato base:** obtiene una copia para guardar o imprimir.

Si algún botón de descarga no aparece, el archivo correspondiente no ha sido cargado aún por el administrador en la carpeta `docs/` del servidor.

---

## 7. PREGUNTAS FREQUENTES

**¿Los precios se muestran en dólares o bolívares?**
El sistema maneja una moneda activa; los precios en la otra moneda se calculan con la **Tasa del Día** registrada en el Dashboard.

**¿Por qué no veo el menú Configuración o Reporte?**
Son módulos exclusivos del rol administrador.

**¿Puedo recuperar una factura eliminada?**
No. Las eliminaciones son definitivas; opere con precaución. Solo el administrador posee acciones de eliminación.

**¿Cada cuánto se actualiza la tasa del día?**
Debe registrarse manualmente cada vez que cambie el valor oficial, idealmente al inicio de la jornada.

**¿Cómo cambio mi contraseña?**
El administrador puede restablecerla desde **Configuración → Usuarios**.

---

## 8. DATOS DE SOPORTE

- **Empresa:** CopiGaby 2023 C.A.
- **RIF:** J-504149357
- Para soporte técnico o ampliaciones del sistema, contacte al administrador o desarrollador responsable.

---

*Documento generado como Manual de Usuario funcional del sistema CopiGaby 2023 C.A. Se recomienda revisarlo al incorporar personal nuevo.*
