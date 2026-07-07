<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Compras</title>
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
        <table class="w-full text-left">
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
              <select name="codigo_proveedor" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
                <?php foreach($proveedores as $p): ?>
                  <option value="<?= $p['codigo_proveedor'] ?>"><?= $p['razon_social'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <a href="?url=proveedor" class="group relative bg-gray-100 p-2.5 rounded-lg text-navy hover:bg-orange hover:text-white transition-colors">
              <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Gestionar Proveedores</span>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
            </a>
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Factura # <span class="text-red-500">*</span></label>
             <input type="text" name="numero_factura_proveedor" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" maxlength="10" placeholder="1002">
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Fecha de Compra <span class="text-red-500">*</span></label>
            <input type="date" name="fecha_compra" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold" value="<?= date('Y-m-d') ?>">
          </div>

          <div class="col-span-2 bg-gray-50 p-4 rounded-xl border-2 border-dashed border-gray-200 mt-2">
              <div class="grid grid-cols-4 gap-2 items-end">
                  <div class="col-span-1">
                      <label class="block text-[0.65rem] font-black text-gray-400 uppercase mb-1">Producto</label>
                      <select id="selProd" class="w-full px-3 py-2 bg-white border rounded-lg text-sm font-bold outline-none focus:border-orange">
                          <option value="">Seleccione...</option>
                          <?php foreach($productos as $pr): ?>
                              <option value="<?= $pr['codigo_producto'] ?>" data-costo="<?= $pr['costo'] ?>"><?= htmlspecialchars($pr['nombre_producto']) ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                  <div class="col-span-1 flex items-center justify-center">
                      <a href="?url=producto" class="group relative text-orange hover:text-orange-dk transition-colors">
                        <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Crear Producto</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                      </a>
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
            <button type="button" onclick="submitCompra()" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">REGISTRAR COMPRA</button>
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
            <input type="date" name="fecha_compra" id="edit_fecha" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
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

  <script>
    const modal = document.getElementById('modalCompra');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => modal.classList.toggle('hidden');
    btnOpen.onclick = toggleModal;

    function openEditModal(id, proveedor, factura, fecha, total, estado) {
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

    function submitCompra() {
        if(items.length === 0) return alert('No puede registrar una compra sin productos. Agregue al menos uno a la lista.');
        
        // Pasamos los datos al input oculto como JSON y enviamos el formulario
        document.getElementById('inputItems').value = JSON.stringify(items);
        document.getElementById('inputTotal').value = total.toFixed(2);
        document.getElementById('formCompra').submit();
    }
  </script>
</body>
</html>
