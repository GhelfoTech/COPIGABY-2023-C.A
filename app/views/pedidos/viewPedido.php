<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Pedidos</title>
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
      <h2 class="text-navy-dark font-extrabold text-xl">GESTION DE PEDIDOS</h2>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <?php if (!empty($pedidoFlash)): ?>
      <div id="pedidoFlash" class="mb-6 px-5 py-4 rounded-xl border font-bold text-sm <?= ($pedidoFlash['status'] ?? '') === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
        <?= htmlspecialchars($pedidoFlash['message'] ?? 'Operación completada.') ?>
      </div>
      <?php endif; ?>
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-[900] text-gray-800">Lista de Pedidos</h1>
          <p class="text-gray-500 text-sm font-semibold">Registro de ventas de productos y servicios</p>
        </div>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          NUEVO PEDIDO
        </button>
      </div>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
              <th class="px-6 py-4">Código</th>
              <th class="px-6 py-4">Cliente</th>
              <th class="px-6 py-4">Atendido por</th>
              <th class="px-6 py-4">Fecha</th>
              <th class="px-6 py-4 text-right">Total</th>
              <th class="px-6 py-4 text-center">Tasa</th>
              <th class="px-6 py-4 text-center">Estado</th>
              <th class="px-6 py-4 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y">
            <?php if (!empty($pedidos)): ?>
            <?php foreach ($pedidos as $ped): ?>
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4">
                  <div class="font-black text-navy-dark">#<?= str_pad($ped['codigo_pedido'], 4, '0', STR_PAD_LEFT) ?></div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-navy-light uppercase font-bold"><?= htmlspecialchars($ped['nombre_cliente']) ?></div>
                </td>
                <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($ped['nombre_usuario']) ?></td>
                <td class="px-6 py-4 text-gray-500"><?= date('d/m/Y H:i', strtotime($ped['fecha_pedido'])) ?></td>
                <td class="px-6 py-4 text-right font-black text-navy-dark">$<?= number_format($ped['monto_total'], 2) ?></td>
                <td class="px-6 py-4 text-center text-gray-400 font-bold"><?= number_format($ped['tasa_aplicada'], 2) ?></td>
                <td class="px-6 py-4 text-center">
                  <span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase <?= $ped['estado'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                    <?= $ped['estado'] ? 'Activo' : 'Anulado' ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="flex justify-center gap-2">
                    <button onclick="openEditPedidoModal(<?= $ped['codigo_pedido'] ?>, <?= $ped['codigo_cliente'] ?>, <?= $ped['tasa_aplicada'] ?>, <?= $ped['estado'] ?>)" class="text-blue-400 hover:text-blue-600 p-2 transition-colors" title="Editar pedido">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <?php if ($ped['estado']): ?>
                    <button onclick="confirmDelete(<?= $ped['codigo_pedido'] ?>)" class="text-red-400 hover:text-red-600 p-2 transition-colors" title="Anular pedido">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" class="px-6 py-10 text-center text-gray-400 font-bold italic">No se encontraron pedidos registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <div id="modalPedido" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" id="overlayPedido"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-4xl animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark tracking-tight">Nuevo Pedido</h3>
          <button type="button" class="text-gray-400 hover:text-navy-dark closeModalPedido">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <form id="formPedido" action="?url=pedido&type=register" method="POST" class="p-6 text-left">
          <input type="hidden" name="items" id="itemsJson">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</label>
              <select name="codigo_cliente" id="selectCliente" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-orange/20 focus:border-orange outline-none font-bold">
                <option value="">— Seleccione un cliente —</option>
                <?php foreach ($clientes as $cli): ?>
                  <option value="<?= $cli['codigo_cliente'] ?>"><?= htmlspecialchars($cli['nombre']) ?> (<?= htmlspecialchars($cli['cedula']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Tasa Aplicada (Bs/$)</label>
              <input type="number" step="0.01" name="tasa_aplicada" id="tasaAplicada" value="<?= htmlspecialchars((string) $tasaActual) ?>" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-orange/20 focus:border-orange outline-none font-bold">
            </div>
          </div>

          <div class="mb-4 flex justify-between items-center">
            <h4 class="text-sm font-black text-navy-dark uppercase tracking-wider">Detalle del Pedido</h4>
            <button type="button" id="btnAddItem" class="flex items-center gap-1 text-xs font-black uppercase text-orange hover:text-orange-dk transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
              Agregar ítem
            </button>
          </div>

          <div class="overflow-x-auto border rounded-lg mb-4">
            <table class="w-full text-left text-sm">
              <thead>
                <tr class="bg-navy-dark text-white text-[0.65rem] uppercase tracking-widest">
                  <th class="px-4 py-3">Tipo</th>
                  <th class="px-4 py-3">Descripción</th>
                  <th class="px-4 py-3 w-24">Cantidad</th>
                  <th class="px-4 py-3 w-32">Precio</th>
                  <th class="px-4 py-3 w-32 text-right">Subtotal</th>
                  <th class="px-4 py-3 w-12"></th>
                </tr>
              </thead>
              <tbody id="itemsBody" class="divide-y font-semibold text-gray-700">
                <tr id="emptyRow">
                  <td colspan="6" class="px-4 py-6 text-center text-gray-400 italic">Agregue al menos un producto o servicio</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex justify-end mb-6">
            <div class="bg-gray-50 border rounded-xl px-6 py-3 text-right">
              <span class="text-xs font-black text-gray-400 uppercase tracking-widest mr-4">Total General</span>
              <span id="totalGeneral" class="text-2xl font-black text-navy-dark">$0.00</span>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button type="button" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg closeModalPedido">Cancelar</button>
            <button type="submit" id="btnSubmitPedido" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy transition-all">Registrar Pedido</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modalEditPedido" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" id="overlayEditPedido"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark tracking-tight">Editar Pedido</h3>
          <button type="button" class="text-gray-400 hover:text-navy-dark" onclick="closeEditPedidoModal()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <form action="?url=pedido&type=update" method="POST" class="p-6 text-left">
          <input type="hidden" name="codigo_pedido" id="edit_codigo_pedido">
          <div class="space-y-4">
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Cliente</label>
              <select name="codigo_cliente" id="edit_codigo_cliente" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-orange/20 focus:border-orange outline-none font-bold">
                <option value="">— Seleccione un cliente —</option>
                <?php foreach ($clientes as $cli): ?>
                  <option value="<?= $cli['codigo_cliente'] ?>"><?= htmlspecialchars($cli['nombre']) ?> (<?= htmlspecialchars($cli['cedula']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Tasa Aplicada (Bs/$)</label>
              <input type="number" step="0.01" min="0.01" name="tasa_aplicada" id="edit_tasa_aplicada" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-orange/20 focus:border-orange outline-none font-bold">
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Estado</label>
              <select name="estado" id="edit_estado" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-orange/20 focus:border-orange outline-none font-bold">
                <option value="1">Activo</option>
                <option value="0">Anulado</option>
              </select>
            </div>
          </div>
          <div class="flex justify-end gap-3 mt-6">
            <button type="button" onclick="closeEditPedidoModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy transition-all">Actualizar Pedido</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const productos = <?= json_encode($productos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const servicios = <?= json_encode($servicios, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    const modalPedido = document.getElementById('modalPedido');
    const btnOpen = document.getElementById('btnOpenModal');
    const closeBtns = document.querySelectorAll('.closeModalPedido');
    const overlayPedido = document.getElementById('overlayPedido');
    const itemsBody = document.getElementById('itemsBody');
    const totalGeneral = document.getElementById('totalGeneral');
    const formPedido = document.getElementById('formPedido');
    const btnSubmitPedido = document.getElementById('btnSubmitPedido');
    const itemsJsonInput = document.getElementById('itemsJson');
    let itemCounter = 0;

    const toggleModal = (forceClose = false) => {
      if (forceClose) {
        modalPedido.classList.add('hidden');
        return;
      }
      modalPedido.classList.toggle('hidden');
    };

    btnOpen.onclick = () => toggleModal();
    closeBtns.forEach(btn => btn.onclick = () => toggleModal(true));
    overlayPedido.onclick = () => toggleModal(true);

    function buildOptions(tipo) {
      const list = tipo === 'producto' ? productos : servicios;
      let html = '<option value="">— Seleccionar —</option>';
      list.forEach(item => {
        const id = tipo === 'producto' ? item.codigo_producto : item.codigo_servicio;
        const nombre = tipo === 'producto' ? item.nombre_producto : item.nombre_servicio;
        const precio = tipo === 'producto' ? item.costo : item.precio;
        const stock = tipo === 'producto' ? item.stock_actual : '';
        const extra = stock !== '' ? ` data-stock="${stock}"` : '';
        html += `<option value="${id}" data-precio="${precio}"${extra}>${nombre}</option>`;
      });
      return html;
    }

    function recalcTotal() {
      let total = 0;
      itemsBody.querySelectorAll('tr[data-item]').forEach(row => {
        total += parseFloat(row.dataset.subtotal || 0);
      });
      totalGeneral.textContent = '$' + total.toFixed(2);
    }

    function normalizeCantidad(input) {
      let value = parseInt(input.value, 10);
      if (isNaN(value) || value < 1) {
        value = 1;
      }
      input.value = String(value);
      return value;
    }

    function updateRowSubtotal(row) {
      const cantidad = normalizeCantidad(row.querySelector('.item-cantidad'));
      const precio = parseFloat(row.querySelector('.item-precio').value) || 0;
      const subtotal = cantidad * precio;
      row.querySelector('.item-subtotal').textContent = '$' + subtotal.toFixed(2);
      row.dataset.subtotal = subtotal.toFixed(2);
      recalcTotal();
    }

    function onTipoChange(row) {
      const tipo = row.querySelector('.item-tipo').value;
      const selectItem = row.querySelector('.item-select');
      selectItem.innerHTML = buildOptions(tipo);
      row.querySelector('.item-precio').value = '';
      row.querySelector('.item-subtotal').textContent = '$0.00';
      row.dataset.subtotal = '0';
      recalcTotal();
    }

    function onItemSelect(row) {
      const selectItem = row.querySelector('.item-select');
      const option = selectItem.options[selectItem.selectedIndex];
      const precio = option ? option.getAttribute('data-precio') : '';
      row.querySelector('.item-precio').value = precio !== null && precio !== '' ? precio : '';
      updateRowSubtotal(row);
    }

    function removeItemRow(tr) {
      tr.remove();
      if (!itemsBody.querySelector('tr[data-item]')) {
        itemsBody.innerHTML = '<tr id="emptyRow"><td colspan="6" class="px-4 py-6 text-center text-gray-400 italic">Agregue al menos un producto o servicio</td></tr>';
        totalGeneral.textContent = '$0.00';
      } else {
        recalcTotal();
      }
    }

    function addItemRow() {
      const emptyRow = document.getElementById('emptyRow');
      if (emptyRow) {
        emptyRow.remove();
      }

      itemCounter += 1;
      const tr = document.createElement('tr');
      tr.id = 'item-' + itemCounter;
      tr.dataset.item = '1';
      tr.dataset.subtotal = '0';
      tr.innerHTML = `
        <td class="px-4 py-3">
          <select class="item-tipo w-full px-2 py-1.5 bg-gray-50 border rounded-lg text-xs font-bold focus:border-orange outline-none">
            <option value="producto">Producto</option>
            <option value="servicio">Servicio</option>
          </select>
        </td>
        <td class="px-4 py-3">
          <select class="item-select w-full px-2 py-1.5 bg-gray-50 border rounded-lg text-xs font-bold focus:border-orange outline-none" required></select>
        </td>
        <td class="px-4 py-3">
          <input type="number" step="1" min="1" value="1" class="item-cantidad w-full px-2 py-1.5 bg-gray-50 border rounded-lg text-xs font-bold focus:border-orange outline-none">
        </td>
        <td class="px-4 py-3">
          <input type="number" step="0.01" min="0" readonly tabindex="-1" class="item-precio w-full px-2 py-1.5 bg-slate-800 text-slate-400 cursor-not-allowed border rounded-lg text-xs font-bold outline-none">
        </td>
        <td class="px-4 py-3 text-right font-black text-navy-dark item-subtotal">$0.00</td>
        <td class="px-4 py-3 text-center">
          <button type="button" class="btn-remove text-red-400 hover:text-red-600 p-1" title="Quitar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </td>
      `;

      itemsBody.appendChild(tr);

      const selectItem = tr.querySelector('.item-select');
      selectItem.innerHTML = buildOptions('producto');

      tr.querySelector('.item-tipo').addEventListener('change', () => onTipoChange(tr));
      selectItem.addEventListener('change', () => onItemSelect(tr));
      tr.querySelector('.item-cantidad').addEventListener('input', () => updateRowSubtotal(tr));
      tr.querySelector('.item-cantidad').addEventListener('change', () => updateRowSubtotal(tr));
      tr.querySelector('.btn-remove').addEventListener('click', () => removeItemRow(tr));
    }

    function collectItemsFromRows() {
      const rows = itemsBody.querySelectorAll('tr[data-item]');
      if (rows.length === 0) {
        return { ok: false, message: 'Debe agregar al menos un ítem al pedido.', items: [] };
      }

      const items = [];

      for (const row of rows) {
        const tipo = row.querySelector('.item-tipo').value;
        const selectItem = row.querySelector('.item-select');
        const id = selectItem.value;
        const cantidad = normalizeCantidad(row.querySelector('.item-cantidad'));
        const precio = parseFloat(row.querySelector('.item-precio').value);

        if (!id) {
          return { ok: false, message: 'Seleccione producto o servicio en cada línea.', items: [] };
        }
        if (isNaN(precio) || precio < 0) {
          return { ok: false, message: 'Precio no válido en una de las líneas.', items: [] };
        }

        const item = { tipo, cantidad, precio_venta: precio };

        if (tipo === 'producto') {
          item.codigo_producto = parseInt(id, 10);
          const option = selectItem.options[selectItem.selectedIndex];
          const stock = parseInt(option.getAttribute('data-stock') || '0', 10);
          if (cantidad > stock) {
            return {
              ok: false,
              message: 'Stock insuficiente para: ' + option.text,
              items: [],
            };
          }
        } else {
          item.codigo_servicio = parseInt(id, 10);
        }

        items.push(item);
      }

      return { ok: true, message: '', items };
    }

    async function submitPedido(e) {
      e.preventDefault();

      const cliente = document.getElementById('selectCliente').value;
      if (!cliente) {
        alert('Seleccione un cliente.');
        return;
      }

      const collected = collectItemsFromRows();
      if (!collected.ok) {
        alert(collected.message);
        return;
      }

      const formData = new FormData();
      formData.append('ajax', '1');
      formData.append('codigo_cliente', cliente);
      formData.append('tasa_aplicada', document.getElementById('tasaAplicada').value);
      formData.append('items', JSON.stringify(collected.items));
      itemsJsonInput.value = JSON.stringify(collected.items);

      const originalLabel = btnSubmitPedido.textContent;
      btnSubmitPedido.disabled = true;
      btnSubmitPedido.textContent = 'Guardando...';

      try {
        const response = await fetch('?url=pedido&type=register', {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: formData,
        });

        const data = await response.json();

        if (data.status === 'success') {
          toggleModal(true);
          location.reload();
          return;
        }

        alert(data.message || 'No se pudo registrar el pedido.');
      } catch (err) {
        console.error('Error al registrar pedido:', err);
        alert('Error de comunicación con el servidor. Revise la consola o el log de PHP.');
      } finally {
        btnSubmitPedido.disabled = false;
        btnSubmitPedido.textContent = originalLabel;
      }
    }

    document.getElementById('btnAddItem').onclick = addItemRow;
    formPedido.addEventListener('submit', submitPedido);

    const modalEditPedido = document.getElementById('modalEditPedido');
    const overlayEditPedido = document.getElementById('overlayEditPedido');

    function openEditPedidoModal(id, clienteId, tasa, estado) {
      document.getElementById('edit_codigo_pedido').value = id;
      document.getElementById('edit_codigo_cliente').value = clienteId;
      document.getElementById('edit_tasa_aplicada').value = tasa;
      document.getElementById('edit_estado').value = String(estado);
      modalEditPedido.classList.remove('hidden');
    }

    function closeEditPedidoModal() {
      modalEditPedido.classList.add('hidden');
    }

    overlayEditPedido.onclick = closeEditPedidoModal;

    function confirmDelete(id) {
      if (!confirm('¿Desea anular este pedido?')) {
        return;
      }
      const f = new FormData();
      f.append('deletePedido', 'true');
      f.append('idpedido', String(id));
      fetch('?url=pedido&type=main', { method: 'POST', body: f })
        .then(r => r.json())
        .then(d => {
          if (d.status === 'success') {
            location.reload();
          } else {
            alert(d.message || 'No se pudo procesar la anulación');
          }
        })
        .catch(() => alert('Error de comunicación al anular el pedido.'));
    }
  </script>
</body>
</html>
