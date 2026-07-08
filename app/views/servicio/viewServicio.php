<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Servicios</title>
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
      <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter">Servicios</h2>
      <div class="flex items-center">
        <?php include 'app/views/layouts/viewUsuarioHeader.php'; ?>
      </div>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-[900] text-gray-800">Catálogo de Servicios</h1>
          <p class="text-gray-500 text-sm font-semibold">Gestione los servicios y sus requerimientos técnicos</p>
        </div>
          <button onclick="abrirModalRegistro()" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          AÑADIR SERVICIO
        </button>
      </div>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
              <th class="px-6 py-4">Código</th>
              <th class="px-6 py-4">Nombre del Servicio</th>
              <th class="px-6 py-4">Descripción</th>
              <th class="px-6 py-4">Precio</th>
              <th class="px-6 py-4">Estado</th>
              <th class="px-6 py-4 text-center">Consultar</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y">
            <?php if(!empty($servicios)): foreach ($servicios as $s): ?>
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4 font-black text-navy-dark">#<?= str_pad($s['codigo_servicio'], 3, '0', STR_PAD_LEFT) ?></td>
                <td class="px-6 py-4 uppercase font-bold text-navy-light"><?= htmlspecialchars($s['nombre_servicio']) ?></td>
                <td class="px-6 py-4 text-gray-500 italic text-xs truncate max-w-xs"><?= htmlspecialchars($s['descripcion']) ?></td>
                <td class="px-6 py-4 font-black text-orange-dk">$<?= number_format($s['precio'], 2) ?></td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase <?= $s['estado'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                    <?= $s['estado'] ? 'Activo' : 'Inactivo' ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <button type="button" onclick='viewDetails(<?= json_encode($s, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="group relative text-orange-dk p-2 hover:bg-orange/10 rounded-lg transition-colors">
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Consultar</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 font-bold italic">No hay servicios registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Modal Registro -->
  <div id="modalRegister" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="toggleModal('modalRegister')"></div>
      <form action="?url=servicio&type=register" method="POST" class="relative bg-white shadow-xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark">Nuevo Servicio</h3>
          <button type="button" onclick="toggleModal('modalRegister')" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6 space-y-4">
          <div>
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre <span class="text-red-500">*</span></label>
              <input type="text" name="nombre_servicio" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="Impresión a color">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
               <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Precio ($) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="precio" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="5.00">
            </div>
          </div>
           <div>
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Descripción</label>
              <textarea name="descripcion" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm h-24" placeholder="Incluye diseño básico y 100 unidades"></textarea>
           </div>

           <div class="border-t border-gray-100 pt-4 mt-2">
             <div class="flex items-center justify-between mb-2">
               <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Materiales / Insumos que consume</label>
               <span class="text-[0.65rem] font-bold text-gray-400 italic">Opcional</span>
             </div>
             <div class="flex flex-wrap md:flex-nowrap gap-2 items-stretch">
               <select id="selProducto_register" class="flex-1 min-w-[200px] px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:border-orange outline-none font-bold text-sm">
                 <option value="">Seleccione un producto / insumo…</option>
                 <?php foreach ($productos as $p): ?>
                   <option value="<?= $p['codigo_producto'] ?>" data-nombre="<?= htmlspecialchars($p['nombre_producto']) ?>" data-stock="<?= (int)($p['stock_actual'] ?? 0) ?>">
                     <?= htmlspecialchars($p['nombre_producto']) ?> (Stock: <?= (int)($p['stock_actual'] ?? 0) ?>)
                   </option>
                 <?php endforeach; ?>
               </select>
               <input type="number" id="cantProducto_register" min="0" step="1" value="1" class="w-28 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="Cantidad">
               <button type="button" onclick="agregarMaterial('register')" class="flex items-center gap-1 px-4 py-2 text-sm font-black bg-gradient-to-r from-orange to-orange-dk text-navy-dark rounded-lg hover:-translate-y-0.5 transition-all shadow-sm">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                 Agregar
               </button>
             </div>
             <div id="listaMateriales_register" class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
             <input type="hidden" name="materiales" id="materiales_register" value="[]">
           </div>

           <div class="flex justify-end gap-3 mt-8">
             <button type="button" onclick="toggleModal('modalRegister')" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
             <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Guardar</button>
           </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Editar -->
  <div id="modalEdit" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="toggleModal('modalEdit')"></div>
      <form action="?url=servicio&type=update" method="POST" class="relative bg-white shadow-xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <input type="hidden" name="idservicio" id="edit_id">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark">Editar Servicio</h3>
          <button type="button" onclick="toggleModal('modalEdit')" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6 space-y-4">
          <div>
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre <span class="text-red-500">*</span></label>
              <input type="text" name="nombre_servicio" id="edit_nombre" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="Impresión a color">
          </div>
          <div>
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Precio <span class="text-red-500">*</span></label>
             <input type="number" step="0.01" name="precio" id="edit_precio" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="5.00">
          </div>
           <div>
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Descripción</label>
              <textarea name="descripcion" id="edit_descripcion" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm h-24" placeholder="Incluye diseño básico y 100 unidades"></textarea>
           </div>

           <div class="border-t border-gray-100 pt-4 mt-2">
             <div class="flex items-center justify-between mb-2">
               <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Materiales / Insumos que consume</label>
               <span class="text-[0.65rem] font-bold text-gray-400 italic">Opcional</span>
             </div>
             <div class="flex flex-wrap md:flex-nowrap gap-2 items-stretch">
               <select id="selProducto_edit" class="flex-1 min-w-[200px] px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:border-orange outline-none font-bold text-sm">
                 <option value="">Seleccione un producto / insumo…</option>
                 <?php foreach ($productos as $p): ?>
                   <option value="<?= $p['codigo_producto'] ?>" data-nombre="<?= htmlspecialchars($p['nombre_producto']) ?>" data-stock="<?= (int)($p['stock_actual'] ?? 0) ?>">
                     <?= htmlspecialchars($p['nombre_producto']) ?> (Stock: <?= (int)($p['stock_actual'] ?? 0) ?>)
                   </option>
                 <?php endforeach; ?>
               </select>
               <input type="number" id="cantProducto_edit" min="0" step="1" value="1" class="w-28 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="Cantidad">
               <button type="button" onclick="agregarMaterial('edit')" class="flex items-center gap-1 px-4 py-2 text-sm font-black bg-gradient-to-r from-orange to-orange-dk text-navy-dark rounded-lg hover:-translate-y-0.5 transition-all shadow-sm">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                 Agregar
               </button>
             </div>
             <div id="listaMateriales_edit" class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
             <input type="hidden" name="materiales" id="materiales_edit" value="[]">
           </div>

           <div class="flex items-center gap-2">
             <input type="checkbox" name="estado" id="edit_estado" class="w-4 h-4 accent-orange">
             <label class="text-sm font-bold text-navy-dark uppercase tracking-widest">Activo</label>
           </div>
           <div class="flex justify-end gap-3 mt-8">
             <button type="button" onclick="toggleModal('modalEdit')" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
             <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Actualizar</button>
           </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Ver Detalle -->
  <div id="modalDetalle" class="fixed inset-0 z-[160] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/80 backdrop-blur-md" onclick="closeDetalleModal()"></div>
      <div class="relative bg-white shadow-2xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark">Detalle del Servicio</h3>
          <button type="button" onclick="closeDetalleModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6 space-y-4">
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Código</p><p id="det_codigo" class="font-black text-navy-dark">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Nombre</p><p id="det_nombre" class="font-bold text-navy-light uppercase">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Precio</p><p id="det_precio" class="font-black text-orange-dk">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Descripción</p><p id="det_descripcion" class="font-semibold text-gray-500 text-sm">—</p></div>
           <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Estado</p><p id="det_estado" class="font-bold">—</p></div>
           <div>
             <p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Materiales / Insumos</p>
             <div id="det_materiales" class="space-y-2"></div>
           </div>
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
    const toggleModal = (id) => document.getElementById(id).classList.toggle('hidden');

    let currentRecord = null;
    let materialesRegister = [];
    let materialesEdit = [];

    function abrirModalRegistro() {
      materialesRegister = [];
      renderMateriales('register');
      toggleModal('modalRegister');
    }

    function agregarMaterial(mode) {
      const sel = document.getElementById('selProducto_' + mode);
      const cant = document.getElementById('cantProducto_' + mode);
      const codigo = sel.value;
      if (!codigo) { alert('Seleccione un producto o insumo.'); return; }

      const nombre = sel.options[sel.selectedIndex].dataset.nombre;
      const stockDisp = parseInt(sel.options[sel.selectedIndex].dataset.stock || '0', 10);
      const cantidadRaw = parseFloat(cant.value);
      const cantidad = isNaN(cantidadRaw) ? 0 : cantidadRaw;

      const arr = mode === 'register' ? materialesRegister : materialesEdit;
      const existente = arr.find(m => String(m.codigo_producto) === String(codigo));
      if (existente) {
        existente.cantidad_usada += cantidad;
      } else {
        arr.push({ codigo_producto: codigo, nombre: nombre, cantidad_usada: cantidad, stock: stockDisp });
      }

      renderMateriales(mode);
      sel.value = '';
      cant.value = 1;
    }

    function quitarMaterial(mode, codigo) {
      if (mode === 'register') {
        materialesRegister = materialesRegister.filter(m => String(m.codigo_producto) !== String(codigo));
      } else {
        materialesEdit = materialesEdit.filter(m => String(m.codigo_producto) !== String(codigo));
      }
      renderMateriales(mode);
    }

    function renderMateriales(mode) {
      const arr = mode === 'register' ? materialesRegister : materialesEdit;
      const lista = document.getElementById('listaMateriales_' + mode);
      const hidden = document.getElementById('materiales_' + mode);
      lista.innerHTML = '';

      if (arr.length === 0) {
        lista.innerHTML = '<p class="col-span-full text-xs text-gray-400 italic">Sin materiales asignados. Puede guardar el servicio sin materiales.</p>';
      } else {
        arr.forEach(m => {
          const card = document.createElement('div');
          card.className = 'flex items-center justify-between gap-2 bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm';
          card.innerHTML =
            '<div class="min-w-0">' +
              '<p class="font-bold text-navy-dark text-sm truncate">' + m.nombre + '</p>' +
              '<p class="text-[0.65rem] text-gray-400">Stock disponible: ' + (m.stock ?? '—') + '</p>' +
            '</div>' +
            '<div class="flex items-center gap-2 shrink-0">' +
              '<span class="text-xs font-black text-orange-dk bg-orange/10 px-2 py-1 rounded-lg">Usa: ' + m.cantidad_usada + '</span>' +
              '<button type="button" onclick="quitarMaterial(\'' + mode + '\', ' + m.codigo_producto + ')" class="w-7 h-7 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 transition-colors" title="Quitar">' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>' +
              '</button>' +
            '</div>';
          lista.appendChild(card);
        });
      }

      const limpios = arr
        .filter(m => parseFloat(m.cantidad_usada) > 0)
        .map(m => ({ codigo_producto: m.codigo_producto, cantidad_usada: m.cantidad_usada }));
      hidden.value = JSON.stringify(limpios);
    }

    function cargarMaterialesServicio(codigo, callback) {
      const f = new FormData();
      f.append('getMaterials', 'true');
      f.append('idservicio', codigo);
      fetch('?url=servicio&type=main', { method: 'POST', body: f })
        .then(r => r.json())
        .then(d => { if (typeof callback === 'function') callback(d); })
        .catch(() => { if (typeof callback === 'function') callback([]); });
    }

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_codigo').textContent = '#' + String(data.codigo_servicio).padStart(3, '0');
      document.getElementById('det_nombre').textContent = data.nombre_servicio;
      document.getElementById('det_precio').textContent = '$' + parseFloat(data.precio).toFixed(2);
      document.getElementById('det_descripcion').textContent = data.descripcion || '—';
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      const cont = document.getElementById('det_materiales');
      cont.innerHTML = '<p class="text-xs text-gray-400 italic">Cargando…</p>';
      cargarMaterialesServicio(data.codigo_servicio, (mats) => {
        if (!mats || mats.length === 0) {
          cont.innerHTML = '<p class="text-xs text-gray-400 italic">Sin materiales asignados.</p>';
          return;
        }
        cont.className = 'grid grid-cols-1 sm:grid-cols-2 gap-2';
        cont.innerHTML = '';
        mats.forEach(m => {
          const card = document.createElement('div');
          card.className = 'flex items-center justify-between gap-2 bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm';
          card.innerHTML =
            '<p class="font-bold text-navy-dark text-sm truncate">' + (m.nombre_producto || ('Prod. #' + m.codigo_producto)) + '</p>' +
            '<span class="text-xs font-black text-orange-dk bg-orange/10 px-2 py-1 rounded-lg shrink-0">Usa: ' + (m.cantidad_usada ?? 0) + '</span>';
          cont.appendChild(card);
        });
      });
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => eliminar(currentRecord.codigo_servicio);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(data) {
      document.getElementById('edit_id').value = data.codigo_servicio;
      document.getElementById('edit_nombre').value = data.nombre_servicio;
      document.getElementById('edit_precio').value = data.precio;
      document.getElementById('edit_descripcion').value = data.descripcion;
      document.getElementById('edit_estado').checked = data.estado == 1;
      materialesEdit = [];
      renderMateriales('edit');
      cargarMaterialesServicio(data.codigo_servicio, (mats) => {
        (mats || []).forEach(m => {
          materialesEdit.push({ codigo_producto: m.codigo_producto, nombre: m.nombre_producto || ('Prod. #' + m.codigo_producto), cantidad_usada: parseFloat(m.cantidad_usada) });
        });
        renderMateriales('edit');
      });
      toggleModal('modalEdit');
    }

    function eliminar(id) {
      if(confirm('¿Desea desactivar este servicio?')) {
        const f = new FormData();
        f.append('deleteServicio', 'true');
        f.append('idservicio', id);
        fetch('?url=servicio&type=main', { method:'POST', body:f })
        .then(r => r.json()).then(d => {
          if(d.status === 'success') location.reload();
          else alert('No se pudo procesar la solicitud.');
        });
      }
    }
  </script>
</body>
</html>