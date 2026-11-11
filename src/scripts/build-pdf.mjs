import fs from 'fs';
import path from 'path';

const SHOTS = 'C:/Users/rodri/Documents/COPIGABY_MANUAL/shots';
const OUT_PDF = 'C:/Users/rodri/Documents/COPIGABY_MANUAL/Manual_Usuario_CopiGaby2023.pdf';
const C = JSON.parse(fs.readFileSync(path.join(SHOTS, 'coords.json'), 'utf8'));

// ---------- figuras con marcas medidas ----------
// marca(n, key) usa coords[key] = {x,y} en % del viewport de la captura.
// El círculo se centra EXACTAMENTE sobre el punto medido (translate(-50%,-50%)).
globalThis.__mkQueue = [];
// mk devuelve un marcador diferido; fig lo resuelve tras fijar el grupo de coordenadas
function mk(key, n, dx = 0, dy = 0) {
  const i = globalThis.__mkQueue.push({ key, n, dx, dy }) - 1;
  return `@@MK${i}@@`;
}

function fig(name, inner = '') {
  const base = name.replace(/\.png$/, '');
  const grupo = C[base] ? base : null;
  const html = inner.replace(/@@MK(\d+)@@/g, (_, i) => {
    const m = globalThis.__mkQueue[+i];
    const c = grupo ? (C[grupo][m.key] || null) : null;
    if (!c) return '';
    globalThis.__mkCount++;
    const x = Math.max(1, Math.min(97, c.x + m.dx)), y = c.y + m.dy;
    return `<div class="mk" style="left:${x}%;top:${y}%"><span class="mk-n">${m.n}</span></div>`;
  });
  return `<div class="fig"><img src="${img64(name)}">${html}</div>`;
}
function img64(name) {
  return `data:image/png;base64,${fs.readFileSync(path.join(SHOTS, name)).toString('base64')}`;
}

// leyenda: array de [num, html]
function ley(items) {
  return `<p class="ley">${items.map(([n, t]) => `<b class="nl">${'①②③④⑤⑥⑦⑧⑨⑩'[n - 1]}</b> ${t}`).join('<br>')}</p>`;
}

// ---------- secciones ----------
const CAPS = []; // {titulo, html}
function cap(titulo, html) { CAPS.push({ titulo, html }); }

// ---- Introducción ----
cap('1. Introducción', `
<p><b>CopiGaby 2023</b> es el sistema de gestión de la empresa CopiGaby 2023 C.A. Con él usted podrá llevar, desde una sola herramienta, todas las operaciones del negocio:</p>
<ul>
<li>Registrar y consultar los <b>pedidos</b> de sus clientes.</li>
<li>Administrar los <b>datos de clientes</b> y sus <b>ventas a crédito (Cashea)</b>.</li>
<li>Controlar las <b>compras</b> a proveedores y el <b>inventario</b> de productos e insumos.</li>
<li>Mantener el <b>catálogo de servicios</b> con sus precios.</li>
<li>Configurar los <b>parámetros del negocio</b>: monedas, impuestos, métodos de pago, unidades de medida y datos de la empresa.</li>
<li>Generar <b>reportes</b> de ventas, inventario y compras.</li>
</ul>
<h3>1.1 ¿Qué necesita para usarlo?</h3>
<ul><li>Un computador con navegador (Chrome, Edge o Firefox).</li>
<li>Su <b>nombre de usuario</b> y <b>contraseña</b>, entregados por el administrador.</li></ul>
<h3>1.2 Roles de usuario</h3>
<table><tr><th>Rol</th><th>Qué puede hacer</th></tr>
<tr><td><b>Administrador</b></td><td>Todo: incluye los menús de Configuración y Reportes, y las acciones de eliminar y modificar en todos los módulos.</td></tr>
<tr><td><b>Empleado</b></td><td>Trabajo diario: registrar y consultar pedidos, clientes, Cashea, productos, categorías, servicios y consultar reportes.</td></tr></table>
<h3>1.3 Cómo leer este manual</h3>
<p>Cada módulo se explica con una <b>captura real del sistema</b> y marcas numeradas ①②③ que señalan los elementos de la pantalla; debajo de cada imagen está la explicación de cada número. Los botones se muestran resaltados: <span class="btnref">AÑADIR CLIENTE</span>.</p>`);

// ---- Login ----
const L = C.login || {};
cap('2. Inicio de sesión', `
<p>Es la primera pantalla del sistema. Debe identificarse para poder usar cualquier módulo.</p>
${fig('01-login.png',
  mk('ingresar', 1, 0, 0))}
${ley([[1, `<b>Botón <span class="btnref">Ingresar al Sistema</span></b>: pulselo después de escribir sus datos para entrar al panel principal.`]])}
<h3>2.1 Elementos de la pantalla</h3>
<ul>
<li><b>Campo USUARIO:</b> escriba el nombre de usuario que le asignó el administrador.</li>
<li><b>Campo CONTRASEÑA:</b> escriba su clave. Con el <b>ícono del ojo 👁</b> alterna entre verla u ocultarla para comprobar lo escrito.</li>
<li><b>Datos de la empresa:</b> al pie se identifica CopiGaby 2023 C.A. y su RIF J-504149357.</li>
</ul>
<h3>2.2 Pasos para ingresar</h3>
<ol><li>Escriba su usuario y contraseña.</li>
<li>(Opcional) pulse el ícono del ojo para verificar la clave.</li>
<li>Pulse <span class="btnref">Ingresar al Sistema</span>.</li></ol>
<p><b>Si algo sale mal:</b> el mensaje <i>"credenciales incorrectas"</i> significa que el usuario o la clave no coinciden (revise mayúsculas). Si olvidó su contraseña, pida al administrador que la restablezca. Al terminar su jornada use <b>Cerrar sesión</b> al final del menú lateral, sobre todo si comparte el equipo.</p>`);

// ---- Dashboard ----
const D = C.dashboard || {};
cap('3. Panel Principal (Dashboard)', `
<p>Es la pantalla que aparece al ingresar: visión instantánea del negocio y actualización de la tasa del día.</p>
${fig('02-dashboard.png',
  mk('moneda', 1) + mk('tasa', 2) + mk('guardar', 3) + mk('historial', 4) + mk('quick', 5))}
${ley([
  [1, `<b>Tipo de moneda</b>: selector de divisa para la tasa.`],
  [2, `<b>Nueva tasa (Bs.)</b>: monto en bolívares por cada unidad de la moneda.`],
  [3, `<b><span class="btnref">GUARDAR TASA</span></b>: registra la tasa con fecha y hora.`],
  [4, `<b>Historial reciente</b>: las últimas 5 tasas registradas.`],
  [5, `<b>Accesos rápidos</b>: atajos a Pedidos, Compras, Inventario y Servicios.`],
])}
<h3>Actualizar la Tasa del Día</h3>
<ol><li>Seleccione la moneda.</li><li>Escriba la nueva tasa en Bs. (admite decimales).</li>
<li>Pulse <span class="btnref">GUARDAR TASA</span>: aparecerá la confirmación y el valor quedará aplicado a todos los precios en bolívares.</li></ol>
<p class="warn">⚠ Actualice la tasa cada día con el valor oficial: de ella dependen los precios en Bs. de todas las ventas.</p>
<p><b>Las tarjetas superiores</b> (Productos Activos, Servicios Ofrecidos, Usuarios Activos, Ingresos del Mes) se actualizan solas; no requieren ninguna acción.</p>`);

// ---- Pedidos ----
cap('4. Módulo de Pedidos', `
<p>Ruta: <b>Menú → Pedido → Pedidos</b>. Registro de ventas de productos y servicios.</p>
${fig('03-pedido.png')}
<p><b>Qué muestra la tabla:</b> cada pedido con su fecha, cliente, monto total (en la moneda activa y su equivalente en Bs.), método de pago y estado (ACTIVO / ANULADO).</p>
<h3>Controles de la pantalla</h3>
<ul>
<li><span class="btnref">+ AÑADIR PEDIDO</span>: abre el formulario de una venta nueva.</li>
<li><b>Buscador:</b> filtra los pedidos al instante por cliente, fecha o monto.</li>
<li><b>Botón 👁</b> de cada fila: abre el detalle completo del pedido con sus renglones.</li>
<li><b>Mostrar [10]</b>: cantidad de filas por página · <b>⇅</b> ordena por columna · <b>paginación</b> avanza entre páginas.</li>
</ul>
<h3>Registrar un pedido</h3>
<ol><li>Pulse <span class="btnref">+ AÑADIR PEDIDO</span>.</li>
<li>Seleccione el <b>cliente</b>.</li>
<li>Agregue <b>productos y/o servicios</b> con sus cantidades; el total se calcula automáticamente con la tasa del día.</li>
<li>Elija el <b>método de pago</b> y guarde.</li></ol>
<p>El inventario se <b>descuenta automáticamente</b> por cada producto vendido. Estos mismos controles (buscar, ordenar, paginar, 👁) se repiten en todos los módulos.</p>`);

// ---- Clientes ----
const CA = C['m-cliente-add'] || {};
cap('5. Módulo de Clientes', `
<p>Ruta: <b>Menú → Pedido → Clientes</b>. Directorio de clientes para las ventas.</p>
${fig('04-cliente.png')}
<p class="ley"><span class="btnref">+ AÑADIR CLIENTE</span> abre la ventana de registro que se explica abajo.</p>
<h3>Ventana "Nuevo Cliente" — cada campo</h3>
${fig('m-cliente-add.png',
  mk('CEDULA', 1) + mk('NOMBRE', 2) + mk('TELEFONO', 3) + mk('CORREO', 4) + mk('DIRECCION', 5) + mk('_cancelar', 6))}
${ley([
  [1, `<b>Cédula *</b> (obligatoria): número de identidad; el prefijo V- es fijo.`],
  [2, `<b>Nombre Completo *</b>: nombre y apellido del cliente.`],
  [3, `<b>Teléfono *</b>: número de contacto.`],
  [4, `<b>Correo Electrónico</b>: opcional.`],
  [5, `<b>Dirección *</b>: domicilio.`],
  [6, `<b>Cancelar</b> cierra la ventana sin guardar (la X superior también). Junto a Cancelar está <span class="btnref">Guardar</span>, que crea el cliente.`],
])}
<h3>Detalle del cliente</h3>
${fig('m-cliente-det.png', mk('MODIFICAR', 1) + mk('ELIMINAR', 2))}
${ley([
  [1, `<span class="btnref">Modificar</span>: abre la misma ventana en modo edición para corregir los datos.`],
  [2, `<span class="btnref">Eliminar</span> (solo administrador): borra el cliente. Evítelo si tiene pedidos asociados.`],
])}`);

// ---- Cashea ----
cap('6. Módulo Cashea (ventas a crédito)', `
<p>Ruta: <b>Menú → Pedido → Cashea</b>. Ventas "pague en cuotas": el cliente lleva el producto y paga por partes.</p>
${fig('05-cashea.png')}
<p class="ley">El listado muestra cada operación con el cliente, monto, lo abonado y el saldo pendiente.</p>
<h3>Cómo usarlo</h3>
<ol><li>Registre la venta a crédito indicando cliente y monto total.</li>
<li>Defina el <b>plan de pagos</b> (cuotas acordadas).</li>
<li>Cada abono del cliente se registra como <b>depósito</b>: el saldo baja automáticamente.</li>
<li>Con el botón 👁 consulte el detalle: historial de abonos y deuda restante.</li></ol>
<p class="tip">💡 Revise periódicamente los saldos pendientes para dar seguimiento a los cobros.</p>`);

// ---- Compras ----
cap('7. Módulo de Compras', `
<p>Ruta: <b>Menú → Compras</b>. Entrada de mercancía: cada compra aumenta el inventario.</p>
${fig('06-compra.png')}
<h3>Registrar una compra</h3>
<ol><li>Pulse <span class="btnref">+ AÑADIR COMPRA</span>.</li>
<li>Seleccione el <b>proveedor</b>.</li>
<li>Agregue los <b>productos</b> con cantidades y costo unitario pagado.</li>
<li>Confirme: el stock <b>aumenta automáticamente</b> y queda registrado el gasto.</li></ol>
<p class="tip">💡 Registre la compra el mismo día en que recibe la mercancía, para que el inventario refleje la realidad.</p>
<p class="ley">La tabla lista cada compra con fecha, proveedor y monto; el botón 👁 muestra los renglones comprados.</p>`);

// ---- Productos ----
const P = C['m-producto-add'] || {};
cap('8. Módulo de Productos e Insumos', `
<p>Ruta: <b>Menú → Producto → Productos-Insumo</b>. Control del inventario.</p>
${fig('07-producto.png')}
<p class="ley"><span class="btnref">+ AÑADIR PRODUCTO</span> abre la ventana de registro que se explica abajo.</p>
<h3>Ventana "Nuevo Producto / Insumo" — cada campo</h3>
${fig('m-producto-add.png',
  mk('NOMBRE', 1) + mk('CATEGORIA', 2) + mk('GANANCIA', 3) + mk('COSTO', 4) + mk('PRECIO_CALC', 5) + mk('DESCRIPCION', 6) + mk('STOCK_ACTUAL', 7) + mk('STOCK_MINIMO', 8) + mk('_cancelar', 9, 0, 0))}
${ley([
  [1, `<b>Nombre del Producto</b> (obligatorio).`],
  [2, `<b>Categoría</b>: escriba y elija de la lista (se crean en el módulo Categoría).`],
  [3, `<b>% Ganancia</b>: margen que se aplicará sobre el costo.`],
  [4, `<b>Costo Unitario</b>: lo que pagó por unidad (se toma de la última compra).`],
  [5, `<b>Precio de venta</b>: el sistema lo calcula solo (costo + ganancia).`],
  [6, `<b>Descripción</b>: detalles opcionales del producto.`],
  [7, `<b>Stock Actual</b>: solo se modifica automáticamente vía Compras y ventas.`],
  [8, `<b>Stock Mínimo</b>: al bajar de este valor el producto aparece en "faltantes" de Reportes.`],
  [9, `<b>Cancelar/Guardar</b>: guarde para crear el producto; Cancelar cierra sin efectos.`],
])}
<h3>Detalle del producto</h3>
${fig('m-producto-det.png', mk('MODIFICAR', 1) + mk('ELIMINAR', 2))}
${ley([
  [1, `<span class="btnref">Modificar</span>: edita código, nombre, precio y demás datos.`],
  [2, `<span class="btnref">Eliminar</span> (solo administrador): saca el producto del inventario.`],
])}`);

// ---- Categorías ----
const K = C['m-categoria-add'] || {};
cap('9. Módulo de Categorías', `
<p>Ruta: <b>Menú → Producto → Categoría</b>. Clasifican los productos del inventario.</p>
${fig('08-categoria.png')}
<p class="ley"><span class="btnref">+ AÑADIR CATEGORÍA</span> abre la ventana de registro.</p>
${K.NOMBRE ? `
<h3>Ventana "Nueva Categoría"</h3>
${fig('m-categoria-add.png', mk('NOMBRE', 1) + mk('_cancelar', 2, 0, 0))}
${ley([
  [1, `<b>Nombre</b> de la categoría (ej. Papelería, Impresión).`],
  [2, `<span class="btnref">Guardar</span> la crea; Cancelar descarta.`],
])}` : ''}
<p class="tip">💡 Cree primero las categorías antes de registrar productos: el formulario de producto las pedirá.</p>`);

// ---- Servicios ----
const S_ = C['m-servicio-add'] || {};
cap('10. Módulo de Servicios', `
<p>Ruta: <b>Menú → Servicios</b>. Catálogo de trabajos (impresiones, fotocopias, anillados...).</p>
${fig('09-servicio.png')}
<p class="ley"><span class="btnref">+ AÑADIR SERVICIO</span> abre la ventana de registro.</p>
<h3>Ventana "Nuevo Servicio" — cada campo</h3>
${fig('m-servicio-add.png',
  (S_.NOMBRE && S_.NOMBRE.x > 0 ? mk('NOMBRE', 1) : '') + mk('PRECIO', 2) + mk('DESCRIPCIÓN', 3) + mk('MATERIALES', 4) + mk('_cancelar', 5, 0, 0))}
${ley([
  ...(S_.NOMBRE && S_.NOMBRE.x > 0 ? [[1, `<b>Nombre</b> del servicio (obligatorio).`]] : []),
  [2, `<b>Precio ($)</b> que se cobrará al cliente.`],
  [3, `<b>Descripción</b>: detalles del servicio (opcional).`],
  [4, `<b>Materiales que consume</b>: si el servicio usa insumos del inventario, búsquelo y agréguelo con la cantidad que consume cada vez.`],
  [5, `<b>Guardar</b> crea el servicio; puede guardarse sin materiales.`],
])}
<h3>Detalle del servicio</h3>
${fig('m-servicio-det.png')}
<p class="ley">Además de los datos, muestra los <b>materiales que consume</b> (ej. HOJAS CARTA ×1): al vender este servicio el sistema descuenta esos insumos del inventario. Incluye los botones Modificar y Eliminar.</p>`);

// ---- Proveedores ----
const V = C['m-proveedor-add'] || {};
cap('11. Módulo de Proveedores', `
<p>Ruta: <b>Menú → Proveedores</b>. Empresas o personas a quienes se les compra la mercancía.</p>
${fig('10-proveedor.png')}
<p class="ley"><span class="btnref">+ AÑADIR PROVEEDOR</span> abre la ventana de registro.</p>
<h3>Ventana "Nuevo Proveedor" — cada campo</h3>
${fig('m-proveedor-add.png',
  mk('RAZON', 1) + mk('RIF', 2) + mk('TELEFONO', 3) + mk('CORREO', 4) + mk('DIRECCION', 5) + mk('_cancelar', 6))}
${ley([
  [1, `<b>Razón Social</b>: nombre del proveedor.`],
  [2, `<b>RIF</b>: elija el prefijo (J-, G-, V-) y escriba el número.`],
  [3, `<b>Teléfono</b> de contacto.`],
  [4, `<b>Correo electrónico</b>.`],
  [5, `<b>Dirección física</b>.`],
  [6, `<b>Guardar</b> registra el proveedor; quedará disponible al registrar Compras.`],
])}
<h3>Detalle del proveedor</h3>
${fig('m-proveedor-det.png', mk('MODIFICAR', 1) + mk('ELIMINAR', 2))}
${ley([
  [1, `<span class="btnref">Modificar</span>: corrige los datos del proveedor.`],
  [2, `<span class="btnref">Eliminar</span> (solo administrador).`],
])}`);

// ---- Config: Usuarios y Roles ----
cap('12. Configuración: Usuarios y Roles', `
<p>Ruta: <b>Menú → Configuración</b> (solo administrador).</p>
<h3>Usuarios</h3>
${fig('11-user.png')}
<p class="ley">Aquí se crean las cuentas del personal: cédula, nombre de usuario, teléfono, contraseña y rol. También se restablecen contraseñas y se desactivan cuentas de quien ya no trabaja en el negocio.</p>
<h3>Roles</h3>
${fig('12-rol.png')}
<p class="ley">Define qué ve y qué puede hacer cada perfil. El <b>Administrador</b> tiene todo; al <b>Empleado</b> se le asignan solo los módulos necesarios. El menú lateral se adapta automáticamente a cada persona.</p>`);

// ---- Config: parámetros ----
cap('13. Configuración: Parámetros comerciales', `
<h3>Método de Pago</h3>
${fig('13-metodopago.png')}
<p class="ley">Medios de pago aceptados (efectivo, Pago Móvil, transferencia, punto de venta, Cashea). Son los que aparecen al registrar una venta.</p>
<h3>Moneda</h3>
${fig('14-moneda.png')}
<p class="ley">Divisas de trabajo. La <b>moneda activa</b> define en qué moneda se registran las ventas; su tasa se administra desde la Tasa del Día.</p>
<h3>IVA</h3>
${fig('15-iva.png')}
<p class="ley">Porcentajes de impuesto disponibles para los productos. Mantenga actualizada la alícuota vigente.</p>
<h3>Unidad de Medida</h3>
${fig('16-medida.png')}
<p class="ley">Cómo se vende cada producto: unidad, paquete, resma, metro...</p>`);

// ---- Config: empresa ----
cap('14. Configuración: Empresa', `
${fig('17-empresa.png')}
<p class="ley">Datos de identidad del negocio: nombre, RIF, dirección, teléfono. Esta información aparece en la pantalla de inicio de sesión y en los documentos del sistema.</p>
<p class="tip">💡 Verifique que el RIF y los datos de contacto estén correctos: acompañan todos los documentos generados.</p>`);

// ---- Reportes ----
cap('15. Módulo de Reportes', `
<p>Ruta: <b>Menú → Reporte</b>. Centro de consultas del negocio.</p>
${fig('18-reporte.png')}
<h3>Generar un reporte de ventas</h3>
<ol><li>Elija la <b>pestaña</b>: <i>Pedido</i> (ventas), <i>Stock</i> (inventario), <i>Lista de faltantes</i> (bajo mínimo) o <i>Compra</i>.</li>
<li>Seleccione el <b>período</b> (todos, hoy, semana, mes...).</li>
<li>Marque en <b>Campos del reporte</b> las columnas deseadas (N° de pedido, fecha, cliente, cédula, atendido por, subtotal, IVA, total, método de pago, estado).</li>
<li>Opcional: <i>"Mostrar los pedidos de mayor monto"</i> y cuántos.</li>
<li>Pulse <span class="btnref">Generar reporte</span>: verá el total de registros y el monto acumulado, seguido de la tabla.</li>
<li>Exporte con <span class="btnref">Descargar PDF</span> o <span class="btnref">Descargar DOCX</span>, o imprima desde el navegador.</li></ol>`);

// ---- Ayuda ----
cap('16. Centro de Ayuda', `
<p>En el menú lateral, al final de la lista y encima de "Cerrar sesión", está la opción <b>Ayuda</b> con el ícono de interrogación:</p>
${fig('modal-ayuda.png')}
<p class="ley">La ventana le recomienda consultar el <b>manual técnico</b>: pulse <span class="btnref">📥 Descargar Manual</span> para obtener este documento, o <span class="btnref">Cancelar</span> para cerrarla sin descargar.</p>
<p class="tip">💡 Este manual siempre está disponible y actualizado con las pantallas reales del sistema.</p>`);

// ---- FAQ ----
cap('17. Preguntas frecuentes y soporte', `
<p><b>¿Los precios se muestran en dólares o bolívares?</b><br>El sistema maneja una moneda activa; los precios en la otra moneda se calculan con la <b>Tasa del Día</b> del Panel Principal.</p>
<p><b>¿Por qué no veo el menú Configuración o Reporte?</b><br>Son exclusivos del administrador. Si le falta algún módulo, pida al administrador revisar los permisos de su rol.</p>
<p><b>¿Puedo recuperar un registro eliminado?</b><br>No. Las eliminaciones son definitivas: opere con precaución.</p>
<p><b>¿Cada cuánto se actualiza la tasa del día?</b><br>Cada vez que cambie el valor oficial; lo ideal es al inicio de la jornada.</p>
<p><b>¿Cómo cambio mi contraseña?</b><br>Solicítelo al administrador desde <i>Configuración → Usuarios</i>.</p>
<p><b>¿Por qué no puedo eliminar un registro?</b><br>Solo el administrador tiene la acción de eliminar.</p>
<h2 style="margin-top:26px;">Soporte</h2>
<p><b>Empresa:</b> CopiGaby 2023 C.A. &nbsp;·&nbsp; <b>RIF:</b> J-504149357<br>
Para soporte del sistema o capacitación de personal nuevo, contacte al administrador.</p>
<p style="color:#64748b;font-style:italic;margin-top:24px;">Manual de Usuario del sistema CopiGaby 2023 C.A. — Documento oficial; revíselo al incorporar personal nuevo.</p>`);

// ================== ENSAMBLADO (2 pasadas: medir -> numerar) ==================
const PAGE_H = 1123; // A4 @96dpi
const PAD_Y = 38 + 55;
const HEADER_INDEX = `<!-- indice -->`;

const CSS = `
<style>
  @page { size: A4; margin: 0; }
  body { font-family:'Segoe UI',Arial,sans-serif; margin:0; color:#1f2937; }
  .pg { position:relative; padding:34px 42px 52px; box-sizing:border-box; page-break-after:always; }
  .pg:last-child { page-break-after:auto; }
  h2 { font-size:15pt; color:#1e293b; border-bottom:3px solid #f97316; padding-bottom:5px; margin:14px 0 10px; }
  h3 { font-size:12pt; color:#334155; margin:12px 0 6px; }
  p, li { font-size:9.5pt; line-height:1.55; margin:5px 0; }
  ul, ol { margin:4px 0 8px 22px; }
  table { border-collapse:collapse; width:100%; margin:8px 0; font-size:9.5pt; }
  th { background:#1e293b; color:white; padding:7px 10px; text-align:left; }
  td { border:1px solid #e2e8f0; padding:7px 10px; }
  tr:nth-child(even) td { background:#f8fafc; }
  table.idx td { border:none; padding:6px 4px; font-size:10.5pt; }
  table.idx tr:nth-child(even) td { background:#f1f5f9; }
  table.idx td.pg { text-align:right; font-weight:800; color:#f97316; width:46px; }
  .btnref { background:#fef3c7; border:1px solid #f97316; border-radius:5px; padding:1px 6px; font-weight:800; color:#7c2d12; font-size:8.5pt; white-space:nowrap; }
  .warn { background:#fef9c3; border-left:4px solid #eab308; padding:8px 12px; border-radius:6px; }
  .tip  { background:#eff6ff; border-left:4px solid #3b82f6; padding:8px 12px; border-radius:6px; }
  .ley { font-size:8.8pt; color:#475569; }
  .nl  { color:#f97316; font-size:11pt; margin-right:3px; }
  .fig { position:relative; margin:6px 0 8px; }
  .fig img { width:100%; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,.08); display:block; }
  .mk { position:absolute; z-index:5; transform:translate(-50%,-50%); }
  .mk-n { display:flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:50%;
          background:#f97316; color:white; font-weight:900; font-size:12.5px; box-shadow:0 1px 5px rgba(0,0,0,.45); }
  .pie { position:absolute; bottom:16px; left:42px; right:42px; font-size:8pt; color:#94a3b8; border-top:1px solid #e2e8f0; padding-top:6px; text-align:center; }
</style>`;

const PIE = `<div class="pie">CopiGaby 2023 C.A. · Manual de Usuario · RIF J-504149357</div>`;

function wrap(content, numPagina) {
  return `<div class="pg">${content}${PIE}${numPagina ? `<div style="position:absolute;top:12px;right:42px;font-size:8.5pt;color:#94a3b8;">Pág. ${numPagina}</div>` : ''}</div>`;
}

export default async function run(page) {
  // PASADA 1: montar el HTML con marcador en el índice, medir y calcular páginas reales
  const idxHtml = CAPS.map((c, i) =>
    `<tr><td>${c.titulo}</td><td class="pg" data-cap="${i}">•</td></tr>`).join('');
  const indice = `<div class="pg"><h2 style="margin-top:0;">Índice de contenido</h2>
    <table class="idx">${idxHtml}</table>
    <p class="ley" style="margin-top:12px;">Cada capítulo indica la página donde comienza. Las capturas incluyen marcas numeradas ①②③ que se explican debajo de cada imagen.</p>
    <div class="pie">CopiGaby 2023 C.A. · Manual de Usuario · RIF J-504149357</div></div>`;

  const portada = `<div class="pg" style="padding:0;">
  <div style="background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%);color:white;padding:70px 45px;text-align:center;">
    <p style="margin:0;font-size:10pt;letter-spacing:6px;color:#94a3b8;">SISTEMA DE GESTIÓN COMERCIAL</p>
    <h1 style="margin:10px 0;font-size:30pt;color:white;">COPIGABY 2023 C.A.</h1>
    <p style="color:#f97316;font-size:13pt;font-weight:800;margin:0 0 24px;">¡SOMOS LA DIFERENCIA!</p>
    <h2 style="font-size:16pt;color:#e2e8f0;border:none;margin:0;">MANUAL DE USUARIO</h2>
    <p style="color:#94a3b8;font-size:9.5pt;margin-top:10px;">Guía completa con capturas reales del sistema · RIF J-504149357</p>
  </div></div>`;

  function construir(paginasPorCap) {
    // divide cada capítulo en tantas páginas como hagan falta
    const partes = [];
    CAPS.forEach((c, i) => {
      const trozos = paginasPorCap[i]; // nº de páginas que ocupa
      if (trozos <= 1) { partes.push(c); return; }
      // dividir por bloques de párrafo/título/figura para que queden balanceados
      const bloques = c.html.match(/<(h2|h3|p|ul|ol|div class="fig"|table)[\s\S]*?<\/(h2|h3|p|ul|ol|div|table)>|<p class="(warn|tip)[\s\S]*?<\/p>/g) || [c.html];
      const mitad = Math.ceil(bloques.length / trozos);
      for (let t = 0; t < trozos; t++) {
        const trozo = bloques.slice(t * mitad, (t + 1) * mitad).join('');
        partes.push({ titulo: c.titulo + (t > 0 ? ' (continuación)' : ''), html: trozo, continuar: t > 0 });
      }
    });
    return partes;
  }

  // Primero montar provisional para medir cuántas páginas ocupa cada capítulo
  const tmpHtml = `<html><head><meta charset="utf-8">${CSS}</head><body>${portada}${indice}${CAPS.map(c => wrap(`<h2>${c.titulo}</h2>${c.html}`))}</body></html>`;
  const tmpPath = path.join(SHOTS, 'tmp.html');
  fs.writeFileSync(tmpPath, tmpHtml);
  await page.setViewportSize({ width: 794, height: 1123 }); // medir en geometría A4
  await page.goto('file:///' + tmpPath.replace(/\\/g, '/'), { waitUntil: 'networkidle' });
  await page.waitForFunction(() =>
    [...document.images].every(img => img.complete && img.naturalHeight > 0), { timeout: 60000 });
  await page.waitForTimeout(2500);

  // medir cada capítulo (scrollHeight del contenido real)
  const alturas = await page.evaluate(() => {
    const pg = [...document.querySelectorAll('.pg')];
    // descartar portada (0) e índice (1)
    return pg.slice(2).map(el => el.scrollHeight);
  });

  const pagPorCap = alturas.map(h => Math.max(1, Math.ceil((h - 20) / (PAGE_H))));
  const partes = construir(pagPorCap);

  // calcular página inicial de cada capítulo (portada=1, índice=2)
  const inicio = []; let n = 2;
  pagPorCap.forEach(p => { inicio.push(n + 1); n += p; });

  const idxReal = CAPS.map((c, i) =>
    `<tr><td>${c.titulo}</td><td class="pg">${inicio[i]}</td></tr>`).join('');
  const indiceReal = indice.replace(idxHtml, idxReal);

  // montar definitivo (pasada 1): los capítulos continúan fluidos dentro de su grupo de páginas
  const cuerpo = partes.map(p => wrap(`${!p.continuar ? `<h2 data-titulo="${p.titulo.replace(/"/g, '')}">${p.titulo}</h2>` : '<p style="color:#94a3b8;font-size:9pt;font-style:italic;">— continuación —</p>'}${p.html}`));

  const finalHtml1 = `<html><head><meta charset="utf-8"><title>Manual CopiGaby 2023</title>${CSS}</head><body>${portada}${indiceReal}${cuerpo.join('')}</body></html>`;
  const outPath = path.join(SHOTS, 'manual.html');
  fs.writeFileSync(outPath, finalHtml1);

  // PASADA FINAL: medir en qué página cae realmente cada título y corregir el índice
  await page.setViewportSize({ width: 794, height: 1123 });
  await page.goto('file:///' + outPath.replace(/\\/g, '/'), { waitUntil: 'networkidle' });
  // esperar a que TODAS las imágenes base64 estén decodificadas y con tamaño final
  await page.waitForFunction(() =>
    [...document.images].every(img => img.complete && img.naturalHeight > 0), { timeout: 60000 });
  await page.waitForTimeout(2500);

  // páginas reales: cada .pg impreso ocupa ceil(alto/1123) hojas de A4;
  // calculamos la hoja de inicio acumulando los capítulos anteriores
  const paginasReales = await page.evaluate(() => {
    const PH = 1123;
    const res = {};
    let acum = 0; // hojas consumidas antes del capítulo actual
    document.querySelectorAll('div.pg').forEach(pg => {
      const h2 = pg.querySelector('h2[data-titulo]');
      if (h2) {
        const yEnPg = h2.getBoundingClientRect().top - pg.getBoundingClientRect().top;
        res[h2.dataset.titulo] = acum + Math.floor(yEnPg / PH) + 1;
      }
      // hojas que consume ESTA página (su contenido puede desbordar)
      acum += Math.max(1, Math.ceil(pg.scrollHeight / PH));
    });
    return res;
  });

  // reconstruir índice con números reales
  const idxFinal = CAPS.map(c =>
    `<tr><td>${c.titulo}</td><td class="pg">${paginasReales[c.titulo] ?? '?'}</td></tr>`).join('');
  const finalHtml2 = finalHtml1.replace(idxHtml, idxFinal);
  fs.writeFileSync(outPath, finalHtml2);

  // imprimir a PDF (con el índice ya corregido)
  await page.goto('file:///' + outPath.replace(/\\/g, '/'), { waitUntil: 'networkidle' });
  await page.waitForTimeout(1500);
  await page.pdf({ path: OUT_PDF, format: 'A4', printBackground: true, displayHeaderFooter: false });

  return { pdf: OUT_PDF, bytes: fs.statSync(OUT_PDF).size, marcas: globalThis.__mkCount, caps: CAPS.map(c => ({ t: c.titulo, pagReal: paginasReales[c.titulo] })) };
}
