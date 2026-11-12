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
              <div class="relative">
                <input type="search" id="buscarProveedorCompra" aria-label="Buscar proveedor por razón social" placeholder="Escriba el nombre del proveedor" class="w-full px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm focus:border-orange">
              </div>
              <select name="codigo_proveedor" id="selectProveedorCompra" required class="hidden">
                <option value="">Seleccione un proveedor</option>
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
                      <div class="relative mb-1">
                          <input type="search" id="buscarProductoCompra" aria-label="Buscar producto por nombre" placeholder="Escriba el nombre del producto" class="w-full px-2 py-1.5 bg-white border rounded-lg text-xs focus:border-orange outline-none">
                      </div>
                      <select id="selProd" class="hidden">
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
                      <input type="number" id="cantProd" class="w-full px-3 py-2 bg-white border rounded-lg text-sm font-bold outline-none focus:border-orange" value="1" min="1" step="1">
                  </div>
                  <div>
                      <label class="block text-[0.65rem] font-black text-gray-400 uppercase mb-1">Costo Unit. ($)</label>
                      <input type="number" step="0.01" min="0.01" id="costProd" placeholder="0.00" class="w-full px-3 py-2 bg-white border rounded-lg text-sm font-bold outline-none focus:border-orange">
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

  <div id="modalDetalle" class="fixed inset-0 z-[160] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/80 backdrop-blur-md" onclick="closeDetalleModal()"></div>
      <div class="relative bg-white shadow-2xl rounded-custom w-full max-w-3xl animate-fade-up overflow-hidden">
        <div class="p-8">
          <div class="border-b pb-6 mb-6">
            <div id="detalleCabeceraLectura" class="flex justify-between items-start">
              <div>
                <h3 class="text-2xl font-black text-navy-dark uppercase tracking-tighter" id="det_factura">FACTURA #000</h3>
                <p class="text-gray-400 font-bold text-sm" id="det_fecha">Fecha: --/--/----</p>
                <p class="mt-2 text-xs font-black uppercase text-gray-500" id="det_estado">Estado: --</p>
              </div>
              <div class="text-right">
                 <p class="text-xs font-black text-orange uppercase tracking-widest">Proveedor</p>
                 <h4 class="font-black text-navy-light uppercase" id="det_proveedor">Nombre Proveedor</h4>
                 <p class="text-gray-500 text-xs font-bold" id="det_rif">RIF: --</p>
              </div>
            </div>

            <div id="detalleCabeceraEdicion" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="sm:col-span-2">
                <label for="buscarProveedorEdicion" class="block text-xs font-black text-gray-500 uppercase mb-1">Proveedor</label>
                <input type="search" id="buscarProveedorEdicion" aria-label="Buscar proveedor por razón social" placeholder="Escriba el nombre del proveedor" class="w-full px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm focus:border-orange">
                <select id="edit_proveedor" required class="hidden">
                  <option value="">Seleccione un proveedor</option>
                  <?php foreach($proveedores as $p): ?>
                    <option value="<?= $p['codigo_proveedor'] ?>"><?= htmlspecialchars($p['razon_social']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label for="edit_factura" class="block text-xs font-black text-gray-500 uppercase mb-1">Factura #</label>
                <input type="text" id="edit_factura" required maxlength="10" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
              </div>
              <div>
                <label for="edit_fecha" class="block text-xs font-black text-gray-500 uppercase mb-1">Fecha de Compra</label>
                <input type="date" id="edit_fecha" required max="<?= date('Y-m-d') ?>" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
              </div>
              <label class="sm:col-span-2 flex items-center gap-3 text-sm font-bold text-gray-700">
                <input type="checkbox" id="edit_estado" class="w-4 h-4 accent-orange">
                Compra Activa
              </label>
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
        <div class="modal-footer bg-gray-50 px-8 py-4 flex flex-wrap justify-end gap-3 border-t">
          <button type="button" id="btnGuardarEdicionCompra" class="hidden px-5 py-2 text-sm font-black text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">Guardar cambios</button>
          <button type="button" id="btnCancelarEdicionCompra" class="hidden px-5 py-2 text-sm font-black text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">Cancelar</button>
          <button type="button" id="btnDetalleEliminar" class="px-5 py-2 text-sm font-black text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Eliminar</button>
          <button type="button" id="btnDetalleEditar" class="px-5 py-2 text-sm font-black text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">Modificar</button>
          <button type="button" id="btnCerrarDetalle" onclick="closeDetalleModal()" class="bg-navy-dark text-white font-black px-8 py-2 rounded-lg text-xs uppercase hover:bg-navy transition-all">Cerrar</button>
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
            <?php $telefonoFieldConfig = ['id' => 'telefonoProveedorCompra']; include 'app/views/layouts/telefonoField.php'; ?>
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
            <div class="flex gap-2">
              <div class="relative min-w-0 flex-1">
                <input type="search" id="buscarCategoriaCompra" aria-label="Buscar categoría por nombre" placeholder="Escriba el nombre de la categoría" class="w-full px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm focus:border-orange">
              </div>
              <button type="button" onclick="openQuickCategoryModal('selectCategoriaCompra')" aria-label="Añadir categoría" title="Añadir categoría" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-orange text-xl font-black text-navy-dark hover:bg-orange-dk">+</button>
            </div>
            <select name="codigo_categoria" id="selectCategoriaCompra" data-categoria-select required class="hidden">
              <option value="">Seleccione una categoría</option>
              <?php foreach($categorias as $categoria): ?>
                <option value="<?= $categoria['codigo_categoria'] ?>"><?= htmlspecialchars($categoria['nombre_categoria']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">% Ganancia <span class="text-red-500">*</span></label>
            <input type="number" step="1" min="0" name="porcentaje_ganancia" required value="0" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold text-sm">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Descripción / Notas</label>
            <textarea name="descripcion" rows="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold text-sm"></textarea>
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Stock Mínimo</label>
            <input type="number" name="stock_minimo" min="0" value="5" step="1" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
          </div>
          <div class="flex items-end justify-end gap-3 col-span-1">
            <button type="button" onclick="closeProductoModal()" class="px-4 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-5 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include 'app/views/categorias/modalQuickCategory.php'; ?>
  <script src="assets/js/searchable-select.js?v=<?= filemtime(__DIR__ . '/../../../assets/js/searchable-select.js') ?>"></script>
  <script src="assets/js/quick-category.js?v=<?= filemtime(__DIR__ . '/../../../assets/js/quick-category.js') ?>"></script>
  <script>
    function openProductoModal() {
      resetSelectFilter(document.getElementById('selectCategoriaCompra'));
      document.getElementById('modalProductoCompra').classList.remove('hidden');
    }

    function closeProductoModal() {
      document.getElementById('modalProductoCompra').classList.add('hidden');
    }

    document.getElementById('formProductoCompra').addEventListener('submit', async function(event) {
      event.preventDefault();
      const formulario = event.currentTarget;
      try {
        const respuesta = await CopiGabyAjax.fetch(formulario.action, { method: 'POST', body: new FormData(formulario) });
        const resultado = await respuesta.json();
        if (!respuesta.ok || resultado.status !== 'success') {
          throw new Error(resultado.message || 'No se pudo registrar el producto');
        }

        const nombreProducto = formulario.elements['nombre_producto'].value.trim().toUpperCase();
        const opcion = new Option(nombreProducto, resultado.codigo_producto, true, true);
        document.getElementById('selProd').add(opcion);
        document.getElementById('selProd').dispatchEvent(new Event('change'));
        formulario.reset();
        closeProductoModal();
        showAlert('El producto fue registrado y quedó seleccionado.', 'success', 'Producto registrado');
      } catch (error) {
        showAlert(error.message || 'No se pudo registrar el producto.', 'error', 'Error');
      }
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
      try {
        const respuesta = await CopiGabyAjax.fetch(formulario.action, {
          method: 'POST',
          body: new FormData(formulario)
        });
        const resultado = await respuesta.json();
        if (!respuesta.ok || resultado.status !== 'success') {
          throw new Error(resultado.message || 'No se pudo registrar el proveedor');
        }

        const razonSocial = formulario.elements['razon_social'].value.trim().toUpperCase();
        const selector = document.getElementById('selectProveedorCompra');
        const opcion = new Option(razonSocial, resultado.codigo_proveedor, true, true);
        selector.add(opcion);
        selector.dispatchEvent(new Event('change'));
        formulario.reset();
        closeProveedorModal();
        showAlert('El proveedor fue registrado y quedó seleccionado.', 'success', 'Proveedor registrado');
      } catch (error) {
        showAlert(error.message || 'No se pudo registrar el proveedor.', 'error', 'Error');
      }
    });

    function resetSelectFilter(select) {
      window.resetSearchableSelect(select);
    }

    window.attachSearchableSelect(
      document.getElementById('buscarProveedorCompra'),
      document.getElementById('selectProveedorCompra')
    );
    window.attachSearchableSelect(
      document.getElementById('buscarProveedorEdicion'),
      document.getElementById('edit_proveedor')
    );
    window.attachSearchableSelect(
      document.getElementById('buscarProductoCompra'),
      document.getElementById('selProd')
    );
    window.attachSearchableSelect(
      document.getElementById('buscarCategoriaCompra'),
      document.getElementById('selectCategoriaCompra')
    );

    const modal = document.getElementById('modalCompra');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => {
      if (modal.classList.contains('hidden')) {
        resetSelectFilter(document.getElementById('selectProveedorCompra'));
        resetSelectFilter(document.getElementById('selProd'));
      }
      modal.classList.toggle('hidden');
    };
    btnOpen.onclick = toggleModal;

    async function confirmDelete(id) {
      if(await showConfirm('¿Desea anular este registro de compra? Esta acción no se puede deshacer.', 'Anular compra', 'Sí, anular')) {
        const f = new FormData();
        f.append('deleteCompra', 'true');
        f.append('idcompra', id);
        CopiGabyAjax.fetch('?url=compra&type=main', { method:'POST', body:f })
        .then(r => r.json())
        .then(d => showMutationResultAndReload(d, 'Compra anulada correctamente.', 'No se pudo anular la compra.'))
        .catch(() => showAlert('No se pudo comunicar con el servidor para anular la compra.', 'error', 'Error de conexión'));
      }
    }

    let detailContext = {};

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function setPurchaseEditMode(enabled) {
      document.querySelectorAll('#tablaDetallesItems [data-purchase-quantity]').forEach(input => {
        input.readOnly = !enabled;
        input.classList.toggle('bg-gray-100', !enabled);
        input.classList.toggle('bg-white', enabled);
      });

      const active = parseInt(detailContext.estado, 10) === 1;
      document.getElementById('detalleCabeceraLectura').classList.toggle('hidden', enabled);
      document.getElementById('detalleCabeceraEdicion').classList.toggle('hidden', !active || !enabled);
      document.getElementById('btnGuardarEdicionCompra').classList.toggle('hidden', !active || !enabled);
      document.getElementById('btnCancelarEdicionCompra').classList.toggle('hidden', !active || !enabled);
      document.getElementById('btnDetalleEditar').classList.toggle('hidden', !active || enabled);
      document.getElementById('btnDetalleEliminar').classList.toggle('hidden', !active || enabled);
      document.getElementById('btnCerrarDetalle').classList.toggle('hidden', enabled);
    }

    function actualizarTotalesDetalleCompra() {
      let total = 0;
      document.querySelectorAll('#tablaDetallesItems [data-purchase-quantity]').forEach(input => {
        const quantity = Number(input.value);
        const cost = Number(input.dataset.purchaseCost);
        const subtotal = Number.isFinite(quantity * cost) ? quantity * cost : 0;
        input.closest('tr').querySelector('[data-purchase-subtotal]').textContent = `$${subtotal.toFixed(2)}`;
        total += subtotal;
      });
      document.getElementById('det_total').textContent = `$${total.toFixed(2)}`;
    }

    async function viewDetails(id) {
      try {
        const response = await CopiGabyAjax.fetch(`?url=compra&type=details&id=${id}`);
        const data = await response.json();
        if (!response.ok || !data.header || !Array.isArray(data.items)) {
          throw new Error('No se pudieron cargar los detalles de la compra.');
        }

          const h = data.header;
          const items = data.items;

          detailContext = {
            id: h.codigo_compra,
            proveedor: h.codigo_proveedor,
            factura: h.numero_factura_proveedor,
            fecha: h.fecha_compra,
            total: h.monto_total,
            estado: h.estado
          };

          document.getElementById('det_factura').innerText = `FACTURA #${h.numero_factura_proveedor}`;
          document.getElementById('det_fecha').innerText = `Fecha de Compra: ${new Date(h.fecha_compra.replace(' ', 'T')).toLocaleDateString('es-VE')}`;
          document.getElementById('det_proveedor').innerText = h.razon_social;
          document.getElementById('det_rif').innerText = `RIF: ${h.rif_proveedor} | Tel: ${h.telefono}`;
          document.getElementById('det_estado').innerText = Number(h.estado) === 1 ? 'Compra Activa' : 'Compra Anulada';
          document.getElementById('det_usuario').innerText = h.nombre_usuario;
          document.getElementById('det_total').innerText = `$${parseFloat(h.monto_total).toFixed(2)}`;
          resetSelectFilter(document.getElementById('edit_proveedor'));
          document.getElementById('edit_proveedor').value = h.codigo_proveedor;
          window.syncSearchableSelect(document.getElementById('edit_proveedor'));
          document.getElementById('edit_factura').value = h.numero_factura_proveedor;
          document.getElementById('edit_fecha').value = h.fecha_compra;
          document.getElementById('edit_estado').checked = Number(h.estado) === 1;

          const tbody = document.querySelector('#tablaDetallesItems tbody');
          tbody.innerHTML = '';
          items.forEach(it => {
            const row = document.createElement('tr');
            const name = document.createElement('td');
            name.className = 'px-4 py-3 uppercase';
            name.textContent = it.nombre_producto;

            const quantityCell = document.createElement('td');
            quantityCell.className = 'px-4 py-3 text-center';
            const quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.min = '1';
            quantityInput.step = '1';
            quantityInput.value = String(it.cantidad);
            quantityInput.readOnly = true;
            quantityInput.dataset.purchaseQuantity = String(it.codigo_detalle_compra);
            quantityInput.dataset.purchaseCost = String(it.costo_unitario);
            quantityInput.setAttribute('aria-label', `Cantidad de ${it.nombre_producto}`);
            quantityInput.className = 'w-24 rounded border bg-gray-100 px-2 py-1 text-center';
            quantityInput.addEventListener('input', actualizarTotalesDetalleCompra);
            quantityCell.appendChild(quantityInput);

            const cost = document.createElement('td');
            cost.className = 'px-4 py-3 text-right';
            cost.textContent = `$${parseFloat(it.costo_unitario).toFixed(2)}`;
            const subtotal = document.createElement('td');
            subtotal.className = 'px-4 py-3 text-right font-black text-orange-dk';
            subtotal.dataset.purchaseSubtotal = '';
            subtotal.textContent = `$${parseFloat(it.subtotal).toFixed(2)}`;

            row.append(name, quantityCell, cost, subtotal);
            tbody.appendChild(row);
          });

          document.getElementById('btnDetalleEditar').onclick = () => setPurchaseEditMode(true);
          document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(detailContext.id);
          document.getElementById('btnCancelarEdicionCompra').onclick = () => viewDetails(detailContext.id);
          document.getElementById('btnGuardarEdicionCompra').onclick = guardarEdicionCompra;
          setPurchaseEditMode(false);
          document.getElementById('modalDetalle').classList.remove('hidden');
          return true;
      } catch (error) {
        showAlert(error.message || 'No se pudieron cargar los detalles de la compra.', 'error', 'Error');
        return false;
      }
    }

    async function guardarEdicionCompra() {
      const inputs = [...document.querySelectorAll('#tablaDetallesItems [data-purchase-quantity]')];
      const items = inputs.map(input => ({
        codigo_detalle_compra: Number(input.dataset.purchaseQuantity),
        cantidad: Number(input.value)
      }));
      const invalidInput = inputs.find(input => !Number.isInteger(Number(input.value)) || Number(input.value) < 1);
      if (invalidInput) {
        invalidInput.focus();
        showAlert('Todas las cantidades deben ser números enteros mayores a cero.', 'warning', 'Cantidad no válida');
        return;
      }

      const proveedor = document.getElementById('edit_proveedor');
      const factura = document.getElementById('edit_factura');
      const fecha = document.getElementById('edit_fecha');
      if (!proveedor.value) {
        showAlert('Seleccione un proveedor para la compra.', 'warning', 'Proveedor requerido');
        return;
      }
      if (!factura.value.trim()) {
        factura.focus();
        showAlert('Indique el número de factura.', 'warning', 'Factura requerida');
        return;
      }
      if (!fecha.value || !fecha.checkValidity()) {
        fecha.reportValidity();
        return;
      }

      const idCompra = detailContext.id;
      const button = document.getElementById('btnGuardarEdicionCompra');
      button.disabled = true;
      const originalText = button.textContent;
      button.textContent = 'Guardando...';
      try {
        const formData = new FormData();
        formData.append('idcompra', String(idCompra));
        formData.append('items', JSON.stringify(items));
        formData.append('codigo_proveedor', proveedor.value);
        formData.append('numero_factura_proveedor', factura.value.trim());
        formData.append('fecha_compra', fecha.value);
        formData.append('estado', document.getElementById('edit_estado').checked ? '1' : '0');
        const response = await CopiGabyAjax.fetch('?url=compra&type=update_details', { method: 'POST', body: formData });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
          throw new Error(result.message || 'No se pudo actualizar la compra.');
        }

        await showAlert(result.message, 'success', 'Compra actualizada');
        window.CopiGabyAjax.refresh();
      } catch (error) {
        showAlert(error.message || 'No se pudo actualizar la compra.', 'error', 'Error');
      } finally {
        button.disabled = false;
        button.textContent = originalText;
      }
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
        
        const cant = Number(cantInput.value);
        const cost = Number(costInput.value);

        if(!sel.value || !Number.isInteger(cant) || cant <= 0 || !Number.isFinite(cost) || cost <= 0 || !costInput.validity.valid) {
            showAlert('Seleccione un producto e indique una cantidad entera positiva y un costo positivo con hasta dos decimales.', 'warning', 'Datos incompletos');
            return;
        }

        const costoNormalizado = Math.round(cost * 100) / 100;

        // Agregar al arreglo de items
        items.push({
            codigo_producto: sel.value,
            nombre: sel.options[sel.selectedIndex].text,
            cantidad: cant,
            costo: costoNormalizado
        });

        cantInput.value = "1";
        sel.value = '';
        window.syncSearchableSelect(sel);
        costInput.value = '';
        
        renderTable();
    }

    function renderTable() {
        const body = document.querySelector('#tablaTemp tbody');
        body.innerHTML = '';
        total = 0;
        items.forEach((it, index) => {
            const sub = Math.round(it.cantidad * it.costo * 100) / 100;
            total = Math.round((total + sub) * 100) / 100;
            body.innerHTML += `<tr><td class="px-4 py-2">${it.nombre}</td><td class="px-4 py-2"><input type="number" min="1" step="1" required value="${it.cantidad}" data-item-quantity="${index}" aria-label="Cantidad de ${it.nombre}" class="w-20 rounded border px-2 py-1 text-center"></td><td class="px-4 py-2">$${it.costo.toFixed(2)}</td><td data-item-subtotal class="px-4 py-2 text-right font-black">$${sub.toFixed(2)}</td></tr>`;
        });
        document.getElementById('txtTotal').innerText = `$${total.toFixed(2)}`;
    }

    const tablaItemsCompra = document.querySelector('#tablaTemp tbody');
    tablaItemsCompra.addEventListener('input', function(event) {
      const input = event.target.closest('[data-item-quantity]');
      if (!input) return;
      const index = Number(input.dataset.itemQuantity);
      const quantity = Number(input.value);
      if (!Number.isInteger(quantity) || quantity <= 0 || !items[index]) return;

      items[index].cantidad = quantity;
      const subtotal = Math.round(quantity * items[index].costo * 100) / 100;
      input.closest('tr').querySelector('[data-item-subtotal]').textContent = `$${subtotal.toFixed(2)}`;
      total = Math.round(items.reduce((sum, item) => sum + item.cantidad * item.costo, 0) * 100) / 100;
      document.getElementById('txtTotal').innerText = `$${total.toFixed(2)}`;
    });

    tablaItemsCompra.addEventListener('change', function(event) {
      const input = event.target.closest('[data-item-quantity]');
      if (!input) return;
      const index = Number(input.dataset.itemQuantity);
      if (!Number.isInteger(Number(input.value)) || Number(input.value) <= 0) {
        input.value = String(items[index].cantidad);
      }
    });

    async function submitCompra() {
        if(items.length === 0) {
            showAlert('No puede registrar una compra sin productos. Agregue al menos uno a la lista.', 'warning', 'Compra sin productos');
            return;
        }

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

        let resultado;
        try {
            const respuesta = await CopiGabyAjax.fetch(formulario.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(formulario),
                signal: controller.signal
            });

            resultado = await respuesta.json();
            if (!respuesta.ok || resultado.status !== 'success') {
                throw new Error(resultado.message || 'No se pudo registrar la compra');
            }
        } catch (error) {
            showAlert(
              error.name === 'AbortError'
                ? 'El registro tardó demasiado tiempo. Revisa si la compra se registró antes de intentar nuevamente.'
                : error.message,
              'error',
              error.name === 'AbortError' ? 'Tiempo agotado' : 'Error al registrar'
            );
            return;
        } finally {
            clearTimeout(timeout);
            boton.disabled = false;
            boton.textContent = 'REGISTRAR COMPRA';
        }

        formulario.reset();
        items = [];
        renderTable();
        modal.classList.add('hidden');
        await showAlert(resultado.message || 'La compra fue registrada correctamente.', 'success', 'Compra registrada');

        try {
            await CopiGabyAjax.refresh();
        } catch (error) {
            console.error('La compra se registró, pero no se pudo actualizar la lista.', error);
            showAlert('La compra se registró, pero no se pudo actualizar la lista. Actualice la vista manualmente.', 'warning', 'Compra registrada');
        }
    }
  </script>
  <script src="assets/js/datatables-init.js?v=4"></script>
</body>
</html>