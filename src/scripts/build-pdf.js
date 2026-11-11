const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const BASE = 'http://localhost:8012/COPIGABY-2023-C.A';
const SHOTS = 'C:/Users/rodri/Documents/COPIGABY_MANUAL/shots';
const OUT = path.join(SHOTS, '..', 'Manual_Usuario_CopiGaby2023.pdf');

// Helper para incrustar imagen en base64
function img64(name) {
  const buf = fs.readFileSync(path.join(SHOTS, name));
  return `data:image/png;base64,${buf.toString('base64')}`;
}

const heroStyle = `
  background: linear-gradient(135deg,#1e293b 0%,#0f172a 100%);
  color:white; padding:60px 50px; text-align:center;
`;
const h1 = (t) => `<h1 style="margin:0;font-size:28pt;font-family:Nunito,A${t}</h1>`;
const h2 = (t) => `<h2 style="margin:0;font-size:15pt;font-family:Nunito,Arial,sans-serif;color:#f97316;font-weight:800;">${t}</h2>`;

const secciones = [];

// ============ PORTADA ============
secciones.push(`
<div style="${heroStyle}">
  <p style="margin:0;font-size:11pt;letter-spacing:6px;color:#94a3b8;">SISTEMA DE GESTIÓN COMERCIAL</p>
  ${h1('COPIGABY 2023 C.A.')}
  <p style="color:#f97316;font-size:14pt;font-weight:800;margin:6px 0 24px;">¡SOMOS LA DIFERENCIA!</p>
  ${h2('MANUAL DE USUARIO')}
  <p style="color:#cbd5e1;font-size:10pt;">Versión 1.0 &nbsp;·&nbsp; Noviembre 2026 &nbsp;·&nbsp; Documento oficial del sistema</p>
  <div style="margin-top:36px;display:inline-block;border:1px solid #334155;border-radius:10px;padding:14px 30px;">
    <p style="margin:0;color:#94a3b8;font-size:9pt;">RIF</p>
    <p style="margin:0;color:white;font-size:13pt;font-weight:800;">J-504149357</p>
  </div>
</div>
<div style="padding:40px 50px;background:#f8fafc;">
  <h3 style="font-size:13pt;color:#1e293b;font-weight:800;">Contenido del manual</h3>
  <ol style="font-size:10pt;color:#475569;line-height:1.9;">
    <li>Introducción y roles de usuario</li>
    <li>Pantalla de Inicio de Sesión</li>
    <li>Panel Principal (Dashboard) y Tasa del Día</li>
    <li>Módulo de Pedidos, Clientes y Cashea</li>
    <li>Módulo de Compras</li>
    <li>Módulo de Productos y Categorías</li>
    <li>Módulo de Servicios</li>
    <li>Módulo de Proveedores</li>
    <li>Configuración: Usuarios, Roles, Métodos de Pago, Monedas, IVA, Unidades de Medida y Empresa</li>
    <li>Módulo de Reportes</li>
    <li>Centro de Ayuda, preguntas frecuentes y soporte</li>
  </ol>
</div>
`);

// ============ 1. INTRODUCCIÓN ============
secciones.push(sec(`
<h2>1. Introducción</h2>
<p><b>CopiGaby 2023</b> es una aplicación web desarrollada en PHP para la gestión integral de la papelería/copistería CopiGaby: control de pedidos, compras a proveedores, inventario de productos e insumos, catálogo de servicios, configuración comercial (monedas, IVA, métodos de pago, unidades de medida) y reportes administrativos.</p>

<h3>1.1 Requisitos</h3>
<ul>
  <li>Navegador web moderno (Chrome, Edge, Firefox).</li>
  <li>Conexión al servidor de la empresa.</li>
  <li>Cuenta de usuario entregada por el administrador.</li>
</ul>

<h3>1.2 Roles de usuario</h3>
<table>
  <tr><th>Rol</th><th>Descripción</th><th>Alcance</th></tr>
  <tr><td><b>Administrador</b></td><td>Acceso total</td><td>Todos los módulos, incluida Configuración y Reportes</td></tr>
  <tr><td><b>Empleado</b></td><td>Acceso limitado</td><td>Pedidos, Clientes, Cashea, Productos, Categorías, Servicios y consulta de Reportes</td></tr>
</table>

<h3>1.3 Convenciones</h3>
<p>Los botones aparecen <span class="btnref">ASÍ</span>. Las rutas se indican como <i>Menú → Submódulo</i>. El sistema valida permisos por rol: si una opción no aparece en su menú, es porque su rol no la tiene asignada.</p>
`));

// ============ 2. LOGIN ============
secciones.push(sec(`
<h2>2. Pantalla de Inicio de Sesión</h2>
<p>Es la puerta de entrada al sistema. Sin sesión iniciada no es posible acceder a ningún módulo. La pantalla muestra la identidad de la empresa: <b>CopiGaby 2023 C.A. · RIF J-504149357</b>.</p>
<img src="${img64('01-login.png')}">
<p><b>Elementos de la pantalla:</b></p>
<ul>
  <li><b>Campo USUARIO:</b> escriba aquí el nombre de usuario asignado.</li>
  <li><b>Campo CONTRASEÑA:</b> clave asignada por el administrador.</li>
  <li><b>Botón 👁 "Mostrar contraseña":</b> alterna entre ver la clave en texto plano o oculta para verificar lo escrito antes de ingresar.</li>
  <li><b>Botón "Ingresar al Sistema":</b> valida las credenciales contra la base de datos y accede al panel principal.</li>
</ul>
<p><b>Pasos:</b> 1) Escriba usuario y contraseña. 2) (Opcional) pulse el ícono del ojo para verificar la clave. 3) Pulse <span class="btnref">Ingresar al Sistema</span>.</p>
<p><b>Mensajes comunes:</b> <i>"Credenciales incorrectas"</i> (revise mayúsculas), sesión expirada (vuelva a iniciar sesión). Si olvida su clave, el administrador puede restablecerla desde Configuración → Usuarios.</p>
`));

// ============ 3. DASHBOARD ============
secciones.push(sec(`
<h2>3. Panel Principal (Dashboard)</h2>
<p>Al ingresar, el sistema muestra el resumen de actividad del negocio. Es la pantalla de inicio para todos los roles.</p>
<img src="${img64('02-dashboard.png')}">

<h3>3.1 Tarjetas de métricas</h3>
<p>Se actualizan automáticamente desde la base de datos:</p>
<ul>
  <li><b>Productos Activos:</b> cantidad de productos/insumos con estatus activo.</li>
  <li><b>Servicios Ofrecidos:</b> total de servicios habilitados en el catálogo.</li>
  <li><b>Usuarios Activos:</b> cuentas de usuario operativas.</li>
  <li><b>Ingresos del Mes:</b> sumatoria de ventas del mes en curso, en dólares ($).</li>
</ul>

<h3>3.2 Módulo de Tasa del Día</h3>
<p>Sección fundamental para trabajar con precios en bolívares (Bs.) cuando la moneda activa es extranjera:</p>
<ul>
  <li><b>Selector "Tipo de moneda":</b> lista las monedas registradas (ej. Dólar USD $, Euro €).</li>
  <li><b>Campo "Nueva tasa (Bs.)":</b> monto en bolívares equivalente a una unidad de la moneda seleccionada, admite decimales.</li>
  <li><b>Botón <span class="btnref">GUARDAR TASA</span>:</b> registra la nueva tasa con fecha y hora automáticas.</li>
</ul>
<p>La tarjeta <b>"Historial reciente"</b> lista las últimas 5 tasas registradas (moneda, fecha/hora y monto).</p>
<p><b>Pasos:</b> 1) Seleccione la moneda. 2) Escriba la nueva tasa en Bs. (solo números positivos con decimales). 3) Pulse <span class="btnref">GUARDAR TASA</span> — el sistema confirmará con un mensaje de éxito.</p>
<p class="warn">⚠ Es responsabilidad diaria del administrador actualizar la tasa al valor oficial del día para que los precios convertidos sean correctos.</p>

<h3>3.3 Accesos rápidos</h3>
<p>Tarjetas de atajo que llevan directamente a las tareas más frecuentes (solo aparecen si su rol tiene permiso):</p>
<ul>
  <li><b>Gestionar pedidos:</b> ir al módulo de ventas y órdenes de clientes.</li>
  <li><b>Registrar compra:</b> registrar entradas de mercancía y actualizar inventario.</li>
  <li><b>Ver inventario:</b> consultar productos e insumos disponibles.</li>
  <li><b>Ver servicios:</b> revisar el catálogo de servicios ofrecidos.</li>
</ul>
`));

// ============ 4. PEDIDOS / CLIENTES / CASHEA ============
secciones.push(sec(`
<h2>4. Módulo de Pedidos</h2>
<p>Ruta: <i>Menú → Pedido → Pedidos</i>. Registro de órdenes de venta de productos y servicios. La cabecera indica la moneda activa y la tasa del día aplicada.</p>
<img src="${img64('03-pedido.png')}">
<p><b>Lista de pedidos:</b> tabla con ID, cliente, usuario que atendió, fecha, monto total (en moneda extranjera y su equivalente en Bs.), método de pago y estado (ACTIVO / ANULADO).</p>
<p><b>Botones y controles:</b></p>
<ul>
  <li><b><span class="btnref">+ AÑADIR PEDIDO</span>:</b> abre el formulario de nueva venta. Seleccione el cliente, agregue productos/servicios con sus cantidades, verifique el total y guarde. El inventario se descuenta automáticamente.</li>
  <li><b>Buscar:</b> filtra pedidos por cliente, fecha, monto o estado.</li>
  <li><b>👁 (Consultar):</b> abre la vista de detalle del pedido seleccionado, con sus renglones y totales.</li>
  <li><b>Mostrar [10]:</b> cambia la cantidad de filas visibles por página.</li>
  <li><b>Encabezados con flechas ⇅:</b> ordenan la tabla por esa columna.</li>
  <li><b>Paginación (Anterior / 1 / Siguiente):</b> navega entre páginas de resultados.</li>
</ul>

<h3>4.1 Módulo de Clientes</h3>
<p>Ruta: <i>Menú → Pedido → Clientes</i>. Agenda de clientes con sus datos de contacto.</p>
<img src="${img64('04-cliente.png')}">
<p>Registre nombre, cédula/RIF, teléfono y dirección con <span class="btnref">+ AÑADIR CLIENTE</span>. Los clientes con pedidos asociados no deben eliminarse para no perder el historial de ventas.</p>

<h3>4.2 Módulo Cashea (ventas a crédito)</h3>
<p>Ruta: <i>Menú → Pedido → Cashea</i>. Gestión de ventas a crédito con plan de pagos por cuotas.</p>
<img src="${img64('05-cashea.png')}">
<p>Registre la venta a crédito, el monto total y el plan de pagos. Consulte el saldo pendiente de cada cliente desde su listado; los abonos se registran como depósitos que disminuyen la deuda.</p>
`));

// ============ 5. COMPRAS ============
secciones.push(sec(`
<h2>5. Módulo de Compras</h2>
<p>Ruta: <i>Menú → Compras</i>. Registro de compras a proveedores: es la vía de entrada de mercancía al inventario.</p>
<img src="${img64('06-compra.png')}">
<p><b>Pasos para registrar una compra:</b></p>
<ol>
  <li>Pulse <span class="btnref">+ AÑADIR COMPRA</span>.</li>
  <li>Seleccione el <b>proveedor</b>.</li>
  <li>Agregue los productos y cantidades compradas con su costo.</li>
  <li>Confirme: el inventario <b>aumenta</b> automáticamente y queda el registro del gasto.</li>
</ol>
<p>La tabla lista cada compra con su fecha, proveedor y monto; use 👁 para consultar el detalle de renglones.</p>
`));

// ============ 6. PRODUCTOS Y CATEGORÍAS ============
secciones.push(sec(`
<h2>6. Módulo de Productos e Insumos</h2>
<p>Ruta: <i>Menú → Producto → Productos-Insumo</i>. Control del inventario: código, nombre, categoría, unidad de medida, precio y existencia.</p>
<img src="${img64('07-producto.png')}">
<p><b>Operaciones:</b></p>
<ul>
  <li><b><span class="btnref">+ AÑADIR PRODUCTO</span>:</b> registra un nuevo producto o insumo con todos sus atributos.</li>
  <li><b>Búsqueda y filtros:</b> ubique cualquier registro del inventario al instante.</li>
  <li><b>Editar / Eliminar:</b> acciones sobre cada fila (eliminar solo para administrador).</li>
</ul>

<h3>6.1 Módulo de Categorías</h3>
<p>Ruta: <i>Menú → Producto → Categoría</i>. Clasificación de los productos del inventario.</p>
<img src="${img64('08-categoria.png')}">
<p><b>Recomendación:</b> cree primero las categorías (ej. "Papelería", "Impresión") y luego registre los productos dentro de ellas.</p>
`));

// ============ 7. SERVICIOS ============
secciones.push(sec(`
<h2>7. Módulo de Servicios</h2>
<p>Ruta: <i>Menú → Servicios</i>. Catálogo de servicios que ofrece el negocio (impresiones, fotocopias, anillados, etc.).</p>
<img src="${img64('09-servicio.png')}">
<p>Defina nombre, descripción y precio con <span class="btnref">+ AÑADIR SERVICIO</span>. Los servicios se agregan a los pedidos igual que los productos, pero <b>no afectan el inventario físico</b>.</p>
`));

// ============ 8. PROVEEDORES ============
secciones.push(sec(`
<h2>8. Módulo de Proveedores</h2>
<p>Ruta: <i>Menú → Proveedores</i>. Gestión de los proveedores de mercancía.</p>
<img src="${img64('10-proveedor.png')}">
<p>Registre razón social, contacto, teléfono y dirección. Estos proveedores son seleccionables al registrar compras en el módulo de Compras.</p>
`));

// ============ 9. CONFIGURACIÓN ============
secciones.push(sec(`
<h2>9. Módulo de Configuración (solo Administrador)</h2>
<p>Ruta: <i>Menú → Configuración</i>. Reúne todos los parámetros maestros del sistema.</p>

<h3>9.1 Usuarios</h3>
<img src="${img64('11-user.png')}">
<p>Creación de cuentas de acceso: cédula, nombre de usuario, teléfono, contraseña y rol. Desde aquí el administrador restablece contraseñas y desactiva cuentas.</p>

<h3>9.2 Roles</h3>
<img src="${img64('12-rol.png')}">
<p>Permisos por perfil. Define qué módulos puede ver y qué acciones (añadir, consultar, modificar, eliminar) puede ejecutar cada rol.</p>

<h3>9.3 Métodos de Pago</h3>
<img src="${img64('13-metodopago.png')}">
<p>Medios de pago aceptados en los pedidos (efectivo, Pago Móvil, transferencia, punto de venta, Cashea).</p>

<h3>9.4 Monedas</h3>
<img src="${img64('14-moneda.png')}">
<p>Monedas de trabajo del sistema y sus tasas de cambio. La moneda activa define en qué divisa se registran las ventas.</p>

<h3>9.5 IVA</h3>
<img src="${img64('15-iva.png')}">
<p>Alícuotas de impuesto disponibles para aplicar a los productos. Mantenga actualizado el porcentaje vigente.</p>

<h3>9.6 Unidad de Medida</h3>
<img src="${img64('16-medida.png')}">
<p>Unidades con las que se venden los productos (unidad, paquete, resma, metro, etc.).</p>

<h3>9.7 Empresa</h3>
<img src="${img64('17-empresa.png')}">
<p>Datos fiscales y de identidad del negocio: nombre, RIF, dirección, teléfono y logo. Esta información aparece en el login y en los documentos generados.</p>
`));

// ============ 10. REPORTES ============
secciones.push(sec(`
<h2>10. Módulo de Reportes</h2>
<p>Ruta: <i>Menú → Reporte</i>. Consulta y análisis de la información del negocio.</p>
<img src="${img64('18-reporte.png')}">
<p>Consulte reportes por rango de fecha para conocer ventas, ingresos y movimiento de inventario. Puede visualizarlos en pantalla o imprimirlos/exportarlos desde el navegador.</p>
`));

// ============ 11. AYUDA + FAQ + SOPORTE ============
secciones.push(sec(`
<h2>11. Centro de Ayuda</h2>
<p>En el menú lateral encontrará la opción <b>Ayuda</b> (identificada con un ícono de interrogación "?"). Al pulsarla se abre una ventana emergente que le recomienda descargar el manual:</p>
<img src="${img64('modal-ayuda.png')}">
<p>Pulse <span class="btnref">📥 Descargar Manual</span> para obtener este documento en PDF. Si prefiere cancelar, pulse <span class="btnref">Cancelar</span> para cerrar la ventana sin descargar nada.</p>

<h2>12. Preguntas Frecuentes</h2>
<p><b>¿Los precios se muestran en dólares o bolívares?</b><br>
El sistema maneja una moneda activa; los precios en la otra moneda se calculan con la <b>Tasa del Día</b> registrada en el Dashboard.</p>
<p><b>¿Por qué no veo el menú Configuración o Reporte?</b><br>
Son módulos exclusivos del rol administrador.</p>
<p><b>¿Puedo recuperar un pedido anulado o eliminado?</b><br>
No. Las eliminaciones son definitivas; opere con precaución. Solo el administrador posee acciones de eliminación.</p>
<p><b>¿Cada cuánto se actualiza la tasa del día?</b><br>
Debe registrarse manualmente cada vez que cambie el valor oficial, idealmente al inicio de la jornada.</p>
<p><b>¿Cómo cambio mi contraseña?</b><br>
El administrador puede restablecerla desde <i>Configuración → Usuarios</i>.</p>

<h2>13. Datos de Soporte</h2>
<p><b>Empresa:</b> CopiGaby 2023 C.A.<br>
<b>RIF:</b> J-504149357<br>
Para soporte técnico o ampliaciones del sistema, contacte al administrador o desarrollador responsable.</p>
<p style="color:#64748b;font-style:italic;">Documento oficial del Manual de Usuario del sistema CopiGaby 2023 C.A. Se recomienda revisarlo al incorporar personal nuevo.</p>
`));

// ---------- Ensamblado ----------
function sec(inner) {
  return `<div class="page">
    <div class="content">${inner}</div>
    <div class="footer">CopiGaby 2023 C.A. · Manual de Usuario · RIF J-504149357</div>
  </div>`;
}

const css = `
<style>
  @page { size: A4; margin: 0; }
  body { font-family: 'Nunito', 'Segoe UI', Arial, sans-serif; margin: 0; color:#1f2937; }
  .page { page-break-after: always; position: relative; padding: 40px 45px 50px; }
  h2 { font-size: 16pt; color:#1e293b; border-bottom:3px solid #f97316; padding-bottom:6px; margin:26px 0 12px; }
  h3 { font-size: 12.5pt; color:#334155; margin:18px 0 8px; }
  p, li { font-size: 10pt; line-height: 1.65; }
  img { width: 100%; border:1px solid #e2e8f0; border-radius: 8px; margin: 10px 0 14px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
  table { border-collapse: collapse; width: 100%; margin: 10px 0; font-size: 9.5pt; }
  th { background:#1e293b; color:white; padding: 8px 10px; text-align:left; }
  td { border:1px solid #e2e8f0; padding: 8px 10px; }
  tr:nth-child(even) td { background:#f8fafc; }
  .btnref { background:#fef3c7; border:1px solid #f97316; border-radius:5px; padding:1px 7px; font-weight:800; color:#7c2d12; font-size:9pt; white-space:nowrap; }
  .warn { background:#fef9c3; border-left:4px solid #eab308; padding:10px 14px; border-radius:6px; }
  .footer { position:absolute; bottom:14px; left:45px; right:45px; font-size:8pt; color:#94a3b8; border-top:1px solid #e2e8f0; padding-top:6px; text-align:center; }
</style>
`;

fs.writeFileSync(path.join(SHOTS, '..', 'manual.html'),
  `<html><head><meta charset="utf-8"><title>Manual CopiGaby 2023</title>${css}</head><body>${secciones.join('')}</body></html>`);

console.log('HTML generado:', path.join(SHOTS, '..', 'manual.html'));
