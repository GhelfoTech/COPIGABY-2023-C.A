<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Compras</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/dist.css">
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="font-['Nunito'] bg-[#f0f2f7] text-[#1f2937] min-h-screen flex">

  <?php include 'app/views/layouts/viewMenuLateral.php'; ?>

  <div class="ml-[260px] flex-1 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b-2 border-orange flex items-center justify-between px-8 sticky top-0 z-50 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
      <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter">Gestión de Compras</h2>
      <div class="flex items-center">
        <?php include 'app/views/layouts/viewUsuarioHeader.php'; ?>
      </div>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-[900] text-gray-800">Módulo de Compras</h1>
          <p class="text-gray-500 text-sm font-semibold">Registro de facturas recibidas de proveedores</p>
        </div>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          AÑADIR COMPRA
        </button>
      </div>

      <?php if (!empty($_SESSION['compra_error'])): ?>
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
          <?= htmlspecialchars($_SESSION['compra_error']) ?>
        </div>
        <?php unset($_SESSION['compra_error']); ?>
      <?php endif; ?>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table data-datatable="true" class="w-full text-left">
          <thead class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
            <tr>
              <th class="px-6 py-4 text-center">ID</th>
              <th class="px-6 py-4">Factura #</th>
              <th class="px-6 py-4">Proveedor</th>
              <th class="px-6 py-4">Fecha</th>
              <th class="px-6 py-4 text-right">Monto Total</th>
              <th class="px-6 py-4 text-center">Estado</th>
              <th class="px-6 py-4 text-center">Consultar</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y">
            <?php foreach ($compras as $c): ?>
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4 text-center text-gray-400 font-bold">#<?= $c['codigo_compra'] ?></td>
                <td class="px-6 py-4 font-black text-navy-dark"><?= $c['numero_factura_proveedor'] ?></td>
                <td class="px-6 py-4 uppercase text-[0.8rem]"><?= htmlspecialchars($c['nombre_proveedor']) ?></td>
                <td class="px-6 py-4 text-gray-500"><?= date('d/m/Y', strtotime($c['fecha_compra'])) ?></td>
                <td class="px-6 py-4 text-right font-black text-navy-dark text-[1rem]">$<?= number_format($c['monto_total'], 2) ?></td>
                <td class="px-6 py-4 text-center">
                  <span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase <?= $c['estado'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                    <?= $c['estado'] ? 'Activa' : 'Inactiva' ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                   <button type="button" onclick="viewDetails(<?= $c['codigo_compra'] ?>)" class="group relative text-orange-dk p-2 hover:bg-orange/10 rounded-lg transition-colors">
                     <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Consultar</span>
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                   </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <div id="modalCompra" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="toggleModal()"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-2xl animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Nueva Compra</h3>
          <button onclick="toggleModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form id="formCompra" action="?url=compra&type=register" method="POST" class="p-6 grid grid-cols-2 gap-4">
          <input type="hidden" name="items" id="inputItems">
          <input type="hidden" name="monto_total" id="inputTotal">
          
          <div class="col-span-2 flex gap-2 items-end">
              <div class="flex-1">
              <label class="block text-xs font-black text-gray-400 uppercase mb-1">Proveedor <span class="text-red-500">*</span></label>
              <div class="flex gap-2 mb-2">
                <input type="search" id="buscarProveedorCompra" aria-label="Buscar proveedor por razón social" placeholder="Buscar proveedor por nombre" class="min-w-0 flex-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm focus:border-orange">
                <button type="button" id="btnBuscarProveedorCompra" aria-label="Buscar proveedor" title="Buscar" class="shrink-0 px-3 bg-orange text-navy-dark rounded-lg hover:bg-orange-dk">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="2"/><path d="m20 20-4-4" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
              </div>
              <select name="codigo_proveedor" id="selectProveedorCompra" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
                <?php foreach($proveedores as $p): ?>
                  <option value="<?= $p['codigo_proveedor'] ?>"><?= $p['razon_social'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="button" onclick="openProveedorModal()" class="group relative bg-gray-100 p-2.5 rounded-lg text-navy hover:bg-orange hover:text-white transition-colors">
              <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Nuevo proveedor</span>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
            </button>
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Factura # <span class="text-red-500">*</span></label>
             <input type="text" name="numero_factura_proveedor" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" maxlength="10" placeholder="1002">
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Fecha de Compra <span class="text-red-500">*</span></label>
            <input type="date" name="fecha_compra" required max="<?= date('Y-m-d') ?>" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold" value="<?= date('Y-m-d') ?>">
          </div>

          <div class="col-span-2 bg-gray-50 p-4 rounded-xl border-2 border-dashed border-gray-200 mt-2">
              <div class="grid grid-cols-4 gap-2 items-end">
                  <div class="col-span-1">
                      <label class="block text-[0.65rem] font-black text-gray-400 uppercase mb-1">Producto</label>
                      <div class="flex gap-1 mb-1">
                          <input type="search" id="buscarProductoCompra" aria-label="Buscar producto por nombre" placeholder="Buscar producto..." class="min-w-0 flex-1 px-2 py-1.5 bg-white border rounded-lg text-xs focus:border-orange outline-none">
                          <button type="button" id="btnBuscarProductoCompra" aria-label="Buscar producto" title="Buscar" class="shrink-0 px-2 bg-orange text-navy-dark rounded-lg hover:bg-orange-dk">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="2"/><path d="m20 20-4-4" stroke-width="2" stroke-linecap="round"/></svg>
                          </button>
                      </div>
                      <select id="selProd" class="w-full px-3 py-2 bg-white border rounded-lg text-sm font-bold outline-none focus:border-orange">
                          <option value="">Seleccione...</option>
                          <?php foreach($productos as $pr): ?>
                              <option value="<?= $pr['codigo_producto'] ?>" data-costo="<?= $pr['costo'] ?>"><?= htmlspecialchars($pr['nombre_producto']) ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                  <div class="col-span-1 flex items-center justify-center">
                      <button type="button" onclick="openProductoModal()" class="group relative text-orange hover:text-orange-dk transition-colors">
                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Crear Producto</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                      </button>
                  </div>
                  <div>
                      <label class="block text-[0.65rem] font-black text-gray-400 uppercase mb-1">Cantidad</label>
                      <input type="number" id="cantProd" class="w-full px-3 py-2 bg-white border rounded-lg text-sm font-bold outline-none focus:border-orange" value="1" min="1">
                  </div>
                  <div>
                      <label class="block text-[0.65rem] font-black text-gray-400 uppercase mb-1">Costo Unit. ($)</label>
                      <input type="number" step="0.01" id="costProd" class="w-full px-3 py-2 bg-white border rounded-lg text-sm font-bold outline-none focus:border-orange">
                  </div>
                  <button type="button" onclick="addItem()" class="col-span-3 bg-navy-light text-white font-bold py-2 rounded-lg text-xs hover:bg-navy transition-colors mt-1 uppercase">Agregar a la lista</button>
              </div>
          </div>

          <!-- Tabla Puente (Detalle Temporal) -->
          <div class="col-span-2 border rounded-xl overflow-hidden mt-2 max-h-48 overflow-y-auto">
              <table class="w-full text-[0.75rem] text-left" id="tablaTemp">
                  <thead class="bg-gray-100 text-gray-500 uppercase font-black">
                      <tr><th class="px-4 py-2">Producto</th><th class="px-4 py-2">Cant.</th><th class="px-4 py-2">Costo</th><th class="px-4 py-2 text-right">Subtotal</th></tr>
                  </thead>
                  <tbody class="divide-y font-bold text-navy-dark"></tbody>
              </table>
          </div>

          <div class="col-span-2 flex justify-between items-center py-2 border-t mt-2">
              <span class="text-xs font-black text-gray-400 uppercase">Monto Total Calculado:</span>
              <span class="text-xl font-black text-orange" id="txtTotal">$0.00</span>
          </div>

          <div class="col-span-2 flex justify-end gap-3 mt-6">
            <button type="button" onclick="toggleModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">CANCELAR</button>
            <button type="button" id="btnRegistrarCompra" onclick="submitCompra()" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">REGISTRAR COMPRA</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modalEditCompra" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Editar Compra</h3>
          <button onclick="closeEditModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=compra&type=update" method="POST" class="p-6 grid grid-cols-2 gap-4">
          <input type="hidden" name="idcompra" id="edit_id">
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Proveedor</label>
            <div class="flex gap-2 mb-2">
              <input type="search" id="buscarProveedorEdicion" aria-label="Buscar proveedor por razón social" placeholder="Buscar proveedor por nombre" class="min-w-0 flex-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm focus:border-orange">
              <button type="button" id="btnBuscarProveedorEdicion" aria-label="Buscar proveedor" title="Buscar" class="shrink-0 px-3 bg-orange text-navy-dark rounded-lg hover:bg-orange-dk">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="2"/><path d="m20 20-4-4" stroke-width="2" stroke-linecap="round"/></svg>
              </button>
            </div>
            <select name="codigo_proveedor" id="edit_proveedor" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
              <?php foreach($proveedores as $p): ?>
                <option value="<?= $p['codigo_proveedor'] ?>"><?= htmlspecialchars($p['razon_social']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Factura #</label>
            <input type="text" name="numero_factura_proveedor" id="edit_factura" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold" maxlength="10">
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Fecha de Compra</label>
            <input type="date" name="fecha_compra" id="edit_fecha" required max="<?= date('Y-m-d') ?>" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Monto Total de Factura ($)</label>
            <input type="number" step="0.01" name="monto_total" id="edit_total" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-lg">
          </div>
          <div class="col-span-2 flex items-center gap-3">
            <input type="checkbox" name="estado" id="edit_estado" class="w-4 h-4 accent-orange">
            <span class="text-sm font-bold text-gray-700">Compra Activa</span>
          </div>
          <div class="col-span-2 flex justify-end gap-3 mt-4">
            <button type="button" onclick="closeEditModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">CANCELAR</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">ACTUALIZAR</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modalDetalle" class="fixed inset-0 z-[160] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/80 backdrop-blur-md" onclick="closeDetalleModal()"></div>
      <div class="relative bg-white shadow-2xl rounded-custom w-full max-w-3xl animate-fade-up overflow-hidden">
        <div class="p-8">
          <div class="flex justify-between items-start border-b pb-6 mb-6">
            <div>
              <h3 class="text-2xl font-black text-navy-dark uppercase tracking-tighter" id="det_factura">FACTURA #000</h3>
              <p class="text-gray-400 font-bold text-sm" id="det_fecha">Fecha: --/--/----</p>
            </div>
            <div class="text-right">
               <p class="text-xs font-black text-orange uppercase tracking-widest">Proveedor</p>
               <h4 class="font-black text-navy-light uppercase" id="det_proveedor">Nombre Proveedor</h4>
               <p class="text-gray-500 text-xs font-bold" id="det_rif">RIF: --</p>
            </div>
          </div>
          
          <div class="mb-6">
             <table class="w-full text-left" id="tablaDetallesItems">
                <thead class="bg-gray-50 text-gray-400 text-[0.65rem] uppercase font-black tracking-widest">
                  <tr><th class="px-4 py-3 text-navy-dark">Producto</th><th class="px-4 py-3 text-center">Cant.</th><th class="px-4 py-3 text-right">Costo Unit.</th><th class="px-4 py-3 text-right">Subtotal</th></tr>
                </thead>
                <tbody class="divide-y text-sm font-bold text-navy-dark"></tbody>
             </table>
          </div>

          <div class="flex justify-between items-end border-t pt-6">
            <div>
              <p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Registrado por</p>
              <p class="text-navy-dark font-bold text-sm" id="det_usuario">Nombre Usuario</p>
            </div>
            <div class="text-right">
               <span class="text-xs font-black text-gray-400 uppercase mr-4">Total de la Compra</span>
               <span class="text-3xl font-black text-navy-dark" id="det_total">$0.00</span>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-gray-50 px-8 py-4 flex justify-end gap-3 border-t">
          <button type="button" id="btnDetalleEliminar" class="px-5 py-2 text-sm font-black text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Eliminar</button>
          <button type="button" id="btnDetalleEditar" class="px-5 py-2 text-sm font-black text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">Modificar</button>
          <button type="button" onclick="closeDetalleModal()" class="bg-navy-dark text-white font-black px-8 py-2 rounded-lg text-xs uppercase hover:bg-navy transition-all">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modalProveedorCompra" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="closeProveedorModal()"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Nuevo Proveedor</h3>
          <button type="button" onclick="closeProveedorModal()" class="text-gray-400 hover:text-navy-dark">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <form id="formProveedorCompra" action="?url=proveedor&type=register" method="POST" class="p-6 grid grid-cols-2 gap-4">
          <input type="hidden" name="origen" value="compra">
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Razón Social <span class="text-red-500">*</span></label>
            <input type="text" name="razon_social" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm">
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">RIF <span class="text-red-500">*</span></label>
            <div class="flex gap-0">
              <select name="rif_tipo" class="px-3 py-2 bg-gray-50 border border-r-0 rounded-l-lg text-sm font-black text-navy-dark outline-none focus:border-orange">
                <option value="J-">J-</option>
                <option value="V-">V-</option>
              </select>
              <input type="tel" inputmode="numeric" pattern="[0-9]*" name="rif_proveedor" required class="w-full px-4 py-2 bg-gray-50 border rounded-r-lg focus:border-orange outline-none font-bold text-sm" placeholder="123456789">
            </div>
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Teléfono <span class="text-red-500">*</span></label>
            <input type="tel" inputmode="numeric" pattern="[0-9]*" name="telefono" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
            <input type="email" name="correo" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Dirección Física</label>
            <textarea name="direccion" rows="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold"></textarea>
          </div>
          <div class="col-span-2 flex justify-end gap-3 mt-4">
            <button type="button" onclick="closeProveedorModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modalProductoCompra" class="fixed inset-0 z-[160] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="closeProductoModal()"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Nuevo Producto / Insumo</h3>
          <button type="button" onclick="closeProductoModal()" class="text-gray-400 hover:text-navy-dark">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <form id="formProductoCompra" action="?url=producto&type=register" method="POST" class="p-6 grid grid-cols-2 gap-4">
          <input type="hidden" name="origen" value="compra">
          <input type="hidden" name="stock_actual" value="0">
          <input type="hidden" name="costo" value="0">
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Nombre del Producto <span class="text-red-500">*</span></label>
            <input type="text" name="nombre_producto" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm">
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Categoría <span class="text-red-500">*</span></label>
            <div class="flex gap-2 mb-2">
              <input type="search" id="buscarCategoriaCompra" aria-label="Buscar categoría por nombre" placeholder="Buscar categoría..." class="min-w-0 flex-1 px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm focus:border-orange">
              <button type="button" id="btnBuscarCategoriaCompra" aria-label="Buscar categoría" title="Buscar" class="shrink-0 px-3 bg-orange text-navy-dark rounded-lg hover:bg-orange-dk">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" stroke-width="2"/><path d="m20 20-4-4" stroke-width="2" stroke-linecap="round"/></svg>
              </button>
            </div>
            <select name="codigo_categoria" id="selectCategoriaCompra" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
              <?php foreach($categorias as $categoria): ?>
                <option value="<?= $categoria['codigo_categoria'] ?>"><?= htmlspecialchars($categoria['nombre_categoria']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">% Ganancia <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" min="0" name="porcentaje_ganancia" required value="0" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold text-sm">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Descripción / Notas</label>
            <textarea name="descripcion" rows="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold text-sm"></textarea>
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Stock Mínimo</label>
            <input type="number" name="stock_minimo" min="0" value="5" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
          </div>
          <div class="flex items-end justify-end gap-3 col-span-1">
            <button type="button" onclick="closeProductoModal()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-5 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function openProductoModal() {
      resetSelectFilter(document.getElementById('buscarCategoriaCompra'), document.getElementById('selectCategoriaCompra'));
      document.getElementById('modalProductoCompra').classList.remove('hidden');
    }

    function closeProductoModal() {
      document.getElementById('modalProductoCompra').classList.add('hidden');
    }

    document.getElementById('formProductoCompra').addEventListener('submit', async function(event) {
      event.preventDefault();
      const formulario = event.currentTarget;
      const respuesta = await fetch(formulario.action, { method: 'POST', body: new FormData(formulario) });
      const resultado = await respuesta.json();

      if (resultado.status !== 'success') {
        alert(resultado.message || 'No se pudo registrar el producto');
        return;
      }

      const nombreProducto = formulario.elements['nombre_producto'].value.trim().toUpperCase();
      const opcion = new Option(nombreProducto, resultado.codigo_producto, true, true);
      document.getElementById('selProd').add(opcion);
      document.getElementById('selProd').dispatchEvent(new Event('change'));
      formulario.reset();
      closeProductoModal();
    });

    function openProveedorModal() {
      document.getElementById('modalProveedorCompra').classList.remove('hidden');
    }

    function closeProveedorModal() {
      document.getElementById('modalProveedorCompra').classList.add('hidden');
    }

    document.getElementById('formProveedorCompra').addEventListener('submit', async function(event) {
      event.preventDefault();

      const formulario = event.currentTarget;
      const respuesta = await fetch(formulario.action, {
        method: 'POST',
        body: new FormData(formulario)
      });
      const resultado = await respuesta.json();

      if (resultado.status !== 'success') {
        alert(resultado.message || 'No se pudo registrar el proveedor');
        return;
      }

      const razonSocial = formulario.elements['razon_social'].value.trim().toUpperCase();
      const selector = document.getElementById('selectProveedorCompra');
      const opcion = new Option(razonSocial, resultado.codigo_proveedor, true, true);
      selector.add(opcion);
      selector.dispatchEvent(new Event('change'));
      formulario.reset();
      closeProveedorModal();
    });

    function resetSelectFilter(input, select) {
      input.value = '';
      [...select.options].forEach(option => { option.hidden = false; });
      select.size = 1;
    }

    function attachSelectFilter(input, select, button, form) {
      const resetFilter = () => {
        resetSelectFilter(input, select);
      };

      const openFilteredOptions = () => {
        const query = input.value.trim().toLocaleLowerCase();
        [...select.options].forEach(option => {
          option.hidden = option.value !== '' && !option.textContent.toLocaleLowerCase().includes(query);
        });
        if (select.selectedOptions.length && select.selectedOptions[0].hidden) {
          select.value = '';
        }
        select.size = 5;
        select.focus();
        if (typeof select.showPicker === 'function') {
          try {
            select.showPicker();
          } catch (error) {
            // The focused list remains available if the browser blocks its native picker.
          }
        }
      };

      button.addEventListener('click', openFilteredOptions);
      input.addEventListener('keydown', event => {
        if (event.key === 'Enter') {
          event.preventDefault();
          openFilteredOptions();
        }
      });
      select.addEventListener('change', resetFilter);
      form.addEventListener('reset', () => window.setTimeout(resetFilter, 0));
    }

    attachSelectFilter(
      document.getElementById('buscarProveedorCompra'),
      document.getElementById('selectProveedorCompra'),
      document.getElementById('btnBuscarProveedorCompra'),
      document.getElementById('formCompra')
    );
    attachSelectFilter(
      document.getElementById('buscarProveedorEdicion'),
      document.getElementById('edit_proveedor'),
      document.getElementById('btnBuscarProveedorEdicion'),
      document.querySelector('#modalEditCompra form')
    );
    attachSelectFilter(
      document.getElementById('buscarProductoCompra'),
      document.getElementById('selProd'),
      document.getElementById('btnBuscarProductoCompra'),
      document.getElementById('formCompra')
    );
    attachSelectFilter(
      document.getElementById('buscarCategoriaCompra'),
      document.getElementById('selectCategoriaCompra'),
      document.getElementById('btnBuscarCategoriaCompra'),
      document.getElementById('formProductoCompra')
    );

    const modal = document.getElementById('modalCompra');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => {
      if (modal.classList.contains('hidden')) {
        resetSelectFilter(document.getElementById('buscarProveedorCompra'), document.getElementById('selectProveedorCompra'));
        resetSelectFilter(document.getElementById('buscarProductoCompra'), document.getElementById('selProd'));
      }
      modal.classList.toggle('hidden');
    };
    btnOpen.onclick = toggleModal;

    function openEditModal(id, proveedor, factura, fecha, total, estado) {
      resetSelectFilter(document.getElementById('buscarProveedorEdicion'), document.getElementById('edit_proveedor'));
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_proveedor').value = proveedor;
      document.getElementById('edit_factura').value = factura;
      document.getElementById('edit_fecha').value = fecha;
      document.getElementById('edit_total').value = total;
      document.getElementById('edit_estado').checked = (estado == 1);
      document.getElementById('modalEditCompra').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('modalEditCompra').classList.add('hidden');
    }

    function confirmDelete(id) {
      if(confirm('¿Desea anular este registro de compra? Esta acción no se puede deshacer.')) {
        const f = new FormData();
        f.append('deleteCompra', 'true');
        f.append('idcompra', id);
        fetch('?url=compra&type=main', { method:'POST', body:f })
        .then(r => r.json()).then(d => {
          if(d.status === 'success') location.reload();
          else alert('No se pudo procesar la anulación');
        });
      }
    }

    let detailContext = {};

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function viewDetails(id) {
      fetch(`?url=compra&type=details&id=${id}`)
      .then(r => r.json())
      .then(data => {
          const h = data.header;
          const items = data.items;
          if (!h) return;

          detailContext = {
            id: h.codigo_compra,
            proveedor: h.codigo_proveedor,
            factura: h.numero_factura_proveedor,
            fecha: h.fecha_compra,
            total: h.monto_total,
            estado: h.estado
          };

          document.getElementById('det_factura').innerText = `FACTURA #${h.numero_factura_proveedor}`;
          document.getElementById('det_fecha').innerText = `Fecha de Compra: ${h.fecha_compra}`;
          document.getElementById('det_proveedor').innerText = h.razon_social;
          document.getElementById('det_rif').innerText = `RIF: ${h.rif_proveedor} | Tel: ${h.telefono}`;
          document.getElementById('det_usuario').innerText = h.nombre_usuario;
          document.getElementById('det_total').innerText = `$${parseFloat(h.monto_total).toFixed(2)}`;

          const tbody = document.querySelector('#tablaDetallesItems tbody');
          tbody.innerHTML = '';
          items.forEach(it => {
            tbody.innerHTML += `
              <tr>
                <td class="px-4 py-3 uppercase">${it.nombre_producto}</td>
                <td class="px-4 py-3 text-center">${it.cantidad}</td>
                <td class="px-4 py-3 text-right">$${parseFloat(it.costo_unitario).toFixed(2)}</td>
                <td class="px-4 py-3 text-right font-black text-orange-dk">$${parseFloat(it.subtotal).toFixed(2)}</td>
              </tr>`;
          });

          const activa = parseInt(h.estado, 10) === 1;
          document.getElementById('btnDetalleEditar').classList.toggle('hidden', !activa);
          document.getElementById('btnDetalleEliminar').classList.toggle('hidden', !activa);
          document.getElementById('btnDetalleEditar').onclick = () => {
            closeDetalleModal();
            openEditModal(detailContext.id, detailContext.proveedor, detailContext.factura, detailContext.fecha, detailContext.total, detailContext.estado);
          };
          document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(detailContext.id);

          document.getElementById('modalDetalle').classList.remove('hidden');
      });
    }

    // Lógica dinámica para gestionar los productos de la compra
    let items = [];
    let total = 0;

    // Al seleccionar un producto, cargar su último costo registrado automáticamente
    document.getElementById('selProd').onchange = function() {
        const opt = this.options[this.selectedIndex];
        if(opt.value) document.getElementById('costProd').value = opt.dataset.costo;
    };

    function addItem() {
        const sel = document.getElementById('selProd');
        const cantInput = document.getElementById('cantProd');
        const costInput = document.getElementById('costProd');
        
        const cant = parseFloat(cantInput.value);
        const cost = parseFloat(costInput.value);

        if(!sel.value || isNaN(cant) || cant <= 0 || isNaN(cost) || cost < 0) {
            alert('Por favor complete los datos del producto (Cantidad y Costo deben ser positivos).');
            return;
        }

        // Agregar al arreglo de items
        items.push({
            codigo_producto: sel.value,
            nombre: sel.options[sel.selectedIndex].text,
            cantidad: cant,
            costo: cost
        });

        // Limpiar campos para la siguiente entrada
        sel.value = "";
        cantInput.value = "1";
        costInput.value = "";
        
        renderTable();
    }

    function renderTable() {
        const body = document.querySelector('#tablaTemp tbody');
        body.innerHTML = '';
        total = 0;
        items.forEach((it) => {
            const sub = it.cantidad * it.costo;
            total += sub;
            body.innerHTML += `<tr><td class="px-4 py-2">${it.nombre}</td><td class="px-4 py-2">${it.cantidad}</td><td class="px-4 py-2">$${it.costo.toFixed(2)}</td><td class="px-4 py-2 text-right font-black">$${sub.toFixed(2)}</td></tr>`;
        });
        document.getElementById('txtTotal').innerText = `$${total.toFixed(2)}`;
    }

    async function submitCompra() {
        if(items.length === 0) return alert('No puede registrar una compra sin productos. Agregue al menos uno a la lista.');

        const formulario = document.getElementById('formCompra');
        if (!formulario.checkValidity()) {
            formulario.reportValidity();
            return;
        }

        document.getElementById('inputItems').value = JSON.stringify(items);
        document.getElementById('inputTotal').value = total.toFixed(2);

        const boton = document.getElementById('btnRegistrarCompra');
        boton.disabled = true;
        boton.textContent = 'REGISTRANDO...';

        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 15000);

        try {
            const respuesta = await fetch(formulario.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(formulario),
                signal: controller.signal
            });
            clearTimeout(timeout);

            const resultado = await respuesta.json();
            if (!respuesta.ok || resultado.status !== 'success') {
                throw new Error(resultado.message || 'No se pudo registrar la compra');
            }

            window.location.replace('?url=compra');
        } catch (error) {
            clearTimeout(timeout);
            boton.disabled = false;
            boton.textContent = 'REGISTRAR COMPRA';
            alert(error.name === 'AbortError' ? 'El registro tardó demasiado tiempo. Revisa si la compra se registró antes de intentar nuevamente.' : error.message);
        }
    }
  </script>
  <script src="assets/js/datatables-init.js"></script>
</body>
</html>
