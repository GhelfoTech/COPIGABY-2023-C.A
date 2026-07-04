<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Productos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="assets/js/tailwind-config.js"></script>
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="font-['Nunito'] bg-[#f0f2f7] text-[#1f2937] min-h-screen flex">

  <?php include 'app/views/layouts/viewMenuLateral.php'; ?>

  <div class="ml-[260px] flex-1 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b-2 border-orange flex items-center justify-between px-8 sticky top-0 z-50 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
      <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter">Gestión de Productos</h2>
      <div class="flex items-center">
        <?php include 'app/views/layouts/viewUsuarioHeader.php'; ?>
      </div>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-[900] text-gray-800">Inventario de Productos</h1>
          <p class="text-gray-500 text-sm font-semibold">Control de existencias e insumos</p>
        </div>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          AÑADIR PRODUCTO
        </button>
      </div>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
          <thead class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
            <tr>
              <th class="px-6 py-4">Producto</th>
              <th class="px-6 py-4">Categoría </th>
              <th class="px-6 py-4">Stock (Act/Min)</th>
              <th class="px-6 py-4">Precio</th>
              <th class="px-6 py-4">Estado</th>
              <th class="px-6 py-4 text-center">Consultar</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y">
            <?php if(!empty($productos)): foreach ($productos as $p): ?>
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4">
                  <div class="font-black text-navy-dark uppercase"><?= htmlspecialchars($p['nombre_producto']) ?></div>
                  <div class="text-[0.7rem] text-gray-400 font-bold">COD: #<?= str_pad($p['codigo_producto'], 4, '0', STR_PAD_LEFT) ?></div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-navy-light"><?= htmlspecialchars($p['nombre_categoria'] ?? '—') ?></div>
                  <span class="inline-block mt-1 px-2 py-0.5 rounded bg-orange/10 text-orange-dk text-[0.65rem] font-black uppercase"><?= floatval($p['porcentaje_ganancia'] ?? 0) ?>% Ganancia</span>
                </td>
                <td class="px-6 py-4">
                  <span class="<?= ($p['stock_actual'] <= $p['stock_minimo']) ? 'text-red-500 font-black' : 'text-green-600' ?>">
                    <?= $p['stock_actual'] ?>
                  </span>
                  <span class="text-gray-300 mx-1">/</span>
                  <span class="text-gray-400 text-[0.75rem] font-bold"><?= $p['stock_minimo'] ?></span>
                </td>
                <td class="px-6 py-4 font-black text-navy-dark">
                  $<?= number_format($p['precio'] ?? ($p['costo'] * (1 + ($p['porcentaje_ganancia'] ?? 0) / 100)), 2) ?>
                </td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase <?= $p['estado'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                    <?= $p['estado'] ? 'Activo' : 'Inactivo' ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <button type="button" onclick='viewDetails(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="group relative text-orange-dk p-2 hover:bg-orange/10 rounded-lg transition-colors">
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Consultar</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 font-bold italic">No hay productos registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <div id="modalProduct" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" id="overlay"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-2xl animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark tracking-tight">Nuevo Producto / Insumo</h3>
          <button class="closeModal"><svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=producto&type=register" method="POST" class="p-6">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre del Producto</label>
              <input type="text" name="nombre_producto" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Categoría</label>
              <select name="codigo_categoria" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
                <?php foreach($categorias as $c): ?>
                  <option value="<?= $c['codigo_categoria'] ?>"><?= $c['nombre_categoria'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">% Ganancia</label>
              <input type="number" step="0.01" min="0" name="porcentaje_ganancia" id="new_porcentaje" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Costo Unitario ($) <span class="text-[0.65rem] font-bold text-gray-400 normal-case">— Se carga desde última compra</span></label>
              <input type="number" step="0.01" min="0" name="costo" id="new_costo" readonly class="w-full px-4 py-2 bg-gray-100 border rounded-lg text-navy-dark font-bold cursor-not-allowed" value="0.00">
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Precio calculado</label>
              <div id="new_precio_preview" class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-lg text-navy-dark font-black">$0.00</div>
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Descripción / Notas</label>
              <textarea name="descripcion" rows="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold"></textarea>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Stock Actual</label>
              <input type="number" name="stock_actual" id="new_stock_actual" value="0" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-navy-dark font-bold cursor-not-allowed">
              <span class="text-[0.65rem] font-bold text-gray-400">Solo se modifica desde Compras</span>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Stock Mínimo</label>
              <input type="number" name="stock_minimo" value="5" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
            </div>
          </div>
          <div class="flex justify-end gap-3 mt-8">
            <button type="button" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg closeModal">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all uppercase tracking-wide">Guardar Producto</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modalEditProduct" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-2xl animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Editar Producto</h3>
          <button onclick="closeEditModal()"><svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=producto&type=update" method="POST" class="p-6">
          <input type="hidden" name="idproducto" id="edit_id">
          <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre del Producto</label>
              <input type="text" name="nombre_producto" id="edit_nombre" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Categoría</label>
              <select name="codigo_categoria" id="edit_categoria" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
                <?php foreach($categorias as $c): ?>
                  <option value="<?= $c['codigo_categoria'] ?>"><?= $c['nombre_categoria'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">% Ganancia</label>
              <input type="number" step="0.01" min="0" name="porcentaje_ganancia" id="edit_porcentaje" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Costo Unitario ($) <span class="text-[0.65rem] font-bold text-gray-400 normal-case">— Se carga desde última compra</span></label>
              <input type="number" step="0.01" min="0" name="costo" id="edit_costo" readonly class="w-full px-4 py-2 bg-gray-100 border rounded-lg text-navy-dark font-bold cursor-not-allowed" value="0.00">
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Precio calculado</label>
              <div id="edit_precio_preview" class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-lg text-navy-dark font-black">$0.00</div>
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Descripción</label>
              <textarea name="descripcion" id="edit_descripcion" rows="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold"></textarea>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Stock Actual</label>
              <input type="number" name="stock_actual" id="edit_stock_actual" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-navy-dark font-bold cursor-not-allowed">
              <span class="text-[0.65rem] font-bold text-gray-400">Solo se modifica desde Compras</span>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Stock Mínimo</label>
              <input type="number" name="stock_minimo" id="edit_stock_minimo" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
            </div>
            <div class="col-span-2 flex items-center gap-2 pt-4">
              <input type="checkbox" name="estado" id="edit_estado" class="w-4 h-4 accent-orange">
              <label class="text-sm font-bold text-navy-dark uppercase tracking-tight">Activo</label>
            </div>
          </div>
          <div class="flex justify-end gap-3 mt-8">
            <button type="button" onclick="closeEditModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Actualizar Producto</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Ver Detalle -->
  <div id="modalDetalle" class="fixed inset-0 z-[160] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/80 backdrop-blur-md" onclick="closeDetalleModal()"></div>
      <div class="relative bg-white shadow-2xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Detalle del Producto</h3>
          <button type="button" onclick="closeDetalleModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4 text-sm">
          <div class="col-span-2"><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Producto</p><p id="det_nombre" class="font-black text-navy-dark uppercase">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Código</p><p id="det_codigo" class="font-bold text-navy-dark">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Estado</p><p id="det_estado" class="font-bold">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Categoría</p><p id="det_categoria" class="font-semibold">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Porcentaje Ganancia</p><p id="det_porcentaje" class="font-semibold text-orange-dk">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Costo Compra</p><p id="det_costo_compra" class="font-bold text-navy-dark">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Precio Venta</p><p id="det_precio" class="font-black text-navy-dark">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Stock (Act / Mín)</p><p id="det_stock" class="font-bold">—</p></div>
          <div class="col-span-2"><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Descripción</p><p id="det_descripcion" class="font-semibold text-gray-600">—</p></div>
        </div>
        <div class="modal-footer bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
          <button type="button" id="btnDetalleEliminar" class="px-5 py-2 text-sm font-black text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Eliminar</button>
          <button type="button" id="btnDetalleEditar" class="px-5 py-2 text-sm font-black text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">Modificar</button>
          <button type="button" onclick="closeDetalleModal()" class="px-6 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy transition-all">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const modal = document.getElementById('modalProduct');
    const btnOpen = document.getElementById('btnOpenModal');
    const closeBtns = document.querySelectorAll('.closeModal');
    const overlay = document.getElementById('overlay');
    const toggleModal = () => modal.classList.toggle('hidden');

    btnOpen.onclick = toggleModal;
    closeBtns.forEach(btn => btn.onclick = toggleModal);
    overlay.onclick = toggleModal;

    const newCostoInput = document.getElementById('new_costo');
    const newPorcentajeInput = document.getElementById('new_porcentaje');
    if (newCostoInput && newPorcentajeInput) {
      const updateNewPrecio = () => updatePricePreview(newCostoInput, newPorcentajeInput, 'new_precio_preview');
      newCostoInput.addEventListener('input', updateNewPrecio);
      newPorcentajeInput.addEventListener('input', updateNewPrecio);
    }

    const editCostoInput = document.getElementById('edit_costo');
    const editPorcentajeInput = document.getElementById('edit_porcentaje');
    if (editCostoInput && editPorcentajeInput) {
      const updateEditPrecio = () => updatePricePreview(editCostoInput, editPorcentajeInput, 'edit_precio_preview');
      editCostoInput.addEventListener('input', updateEditPrecio);
      editPorcentajeInput.addEventListener('input', updateEditPrecio);
    }

    let currentRecord = null;

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_nombre').textContent = data.nombre_producto;
      document.getElementById('det_codigo').textContent = '#' + String(data.codigo_producto).padStart(4, '0');
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      document.getElementById('det_categoria').textContent = data.nombre_categoria || '—';
      document.getElementById('det_porcentaje').textContent = parseFloat(data.porcentaje_ganancia || data.porcentaje_ganancia || 0).toFixed(2) + '%';
      document.getElementById('det_costo_compra').textContent = '$' + parseFloat(data.costo_compra || data.costo || 0).toFixed(2);
      document.getElementById('det_precio').textContent = '$' + parseFloat(data.precio || 0).toFixed(2);
      document.getElementById('det_stock').textContent = data.stock_actual + ' / ' + data.stock_minimo;
      document.getElementById('det_descripcion').textContent = data.descripcion || '—';
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(currentRecord.codigo_producto);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function calculateProductPrice(costo, porcentaje) {
      const cost = parseFloat(costo) || 0;
      const gain = parseFloat(porcentaje) || 0;
      return cost * (1 + gain / 100);
    }

    function updatePricePreview(costoInput, porcentajeInput, previewId) {
      const costo = parseFloat(costoInput.value) || 0;
      const porcentaje = parseFloat(porcentajeInput.value) || 0;
      const precio = calculateProductPrice(costo, porcentaje);
      document.getElementById(previewId).textContent = '$' + precio.toFixed(2);
    }

    function openEditModal(data) {
        document.getElementById('edit_id').value = data.codigo_producto;
        document.getElementById('edit_nombre').value = data.nombre_producto;
        document.getElementById('edit_categoria').value = data.codigo_categoria;
        const costoCompra = parseFloat(data.costo_compra || data.costo || 0).toFixed(2);
        document.getElementById('edit_costo').value = costoCompra;
        document.getElementById('edit_porcentaje').value = parseFloat(data.porcentaje_ganancia || data.porcentaje_ganancia || 0).toFixed(2);
        document.getElementById('edit_precio_preview').textContent = '$' + calculateProductPrice(costoCompra, data.porcentaje_ganancia || data.porcentaje_ganancia || 0).toFixed(2);
        document.getElementById('edit_descripcion').value = data.descripcion;
        document.getElementById('edit_stock_actual').value = data.stock_actual;
        document.getElementById('edit_stock_minimo').value = data.stock_minimo;
        document.getElementById('edit_estado').checked = (data.estado == 1);
        document.getElementById('modalEditProduct').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEditProduct').classList.add('hidden');
    }

    function confirmDelete(id) {
      if(confirm('¿Desea desactivar este producto del inventario?')) {
        const f = new FormData();
        f.append('deleteProduct', 'true');
        f.append('idproducto', id);
        fetch('?url=producto&type=main', { method:'POST', body:f })
        .then(r => r.json()).then(d => {
          if(d.status === 'success') location.reload();
          else alert('No se pudo procesar');
        });
      }
    }
  </script>
</body>
</html>
