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
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-[900] text-gray-800">Inventario de Productos</h1>
          <p class="text-gray-500 text-sm font-semibold">Control de existencias e insumos</p>
        </div>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          NUEVO PRODUCTO
        </button>
      </div>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
          <thead class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
            <tr>
              <th class="px-6 py-4">Producto</th>
              <th class="px-6 py-4">Categoría / IVA</th>
              <th class="px-6 py-4">Stock (Act/Min)</th>
              <th class="px-6 py-4">Costo</th>
              <th class="px-6 py-4">Unidad</th>
              <th class="px-6 py-4">Estado</th>
              <th class="px-6 py-4 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y">
            <?php foreach ($productos as $p): ?>
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4">
                  <div class="font-black text-navy-dark uppercase"><?= htmlspecialchars($p['nombre_producto']) ?></div>
                  <div class="text-[0.7rem] text-gray-400 font-bold">COD: #<?= str_pad($p['codigo_producto'], 4, '0', STR_PAD_LEFT) ?></div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-navy-light"><?= htmlspecialchars($p['nombre_categoria']) ?></div>
                  <div class="text-[0.7rem] text-orange-dk font-bold italic">IVA: <?= $p['porcentaje_iva'] ?>%</div>
                </td>
                <td class="px-6 py-4">
                  <span class="<?= ($p['stock_actual'] <= $p['stock_minimo']) ? 'text-red-500 font-black' : 'text-green-600' ?>">
                    <?= $p['stock_actual'] ?>
                  </span>
                  <span class="text-gray-300 mx-1">/</span>
                  <span class="text-gray-400 text-[0.75rem] font-bold"><?= $p['stock_minimo'] ?></span>
                </td>
                <td class="px-6 py-4 text-gray-500 font-bold">
                  <?= htmlspecialchars($p['nombre_medida']) ?>
                </td>
                <td class="px-6 py-4 font-black text-navy-dark">
                  $<?= number_format($p['costo'], 2) ?>
                </td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase <?= $p['estado'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                    <?= $p['estado'] ? 'Activo' : 'Inactivo' ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="flex justify-center gap-2">
                    <button onclick='openEditModal(<?= json_encode($p) ?>)' class="text-blue-500 p-2 hover:bg-blue-50 rounded-lg transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="confirmDelete(<?= $p['codigo_producto'] ?>)" class="text-red-500 p-2 hover:bg-red-50 rounded-lg transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
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
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">IVA (%)</label>
              <select name="codigo_IVA" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none focus:border-orange font-bold">
                <?php foreach($ivas as $i): ?>
                  <option value="<?= $i['codigo_IVA'] ?>"><?= $i['porcentaje_iva'] ?>%</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Unidad de Medida</label>
              <select name="codigo_medida" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none focus:border-orange font-bold">
                <?php foreach($medidas as $m): ?>
                  <option value="<?= $m['codigo_media'] ?>"><?= $m['nombre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Descripción / Notas</label>
              <textarea name="descripcion" rows="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold"></textarea>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Stock Actual</label>
              <input type="number" name="stock_actual" value="0" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Stock Mínimo</label>
              <input type="number" name="stock_minimo" value="5" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Costo Unitario ($)</label>
              <input type="number" step="0.01" name="costo" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
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
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">IVA (%)</label>
              <select name="codigo_IVA" id="edit_iva" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
                <?php foreach($ivas as $i): ?>
                  <option value="<?= $i['codigo_IVA'] ?>"><?= $i['porcentaje_iva'] ?>%</option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Unidad de Medida</label>
              <select name="codigo_medida" id="edit_medida" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
                <?php foreach($medidas as $m): ?>
                  <option value="<?= $m['codigo_media'] ?>"><?= $m['nombre'] ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Descripción</label>
              <textarea name="descripcion" id="edit_descripcion" rows="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold"></textarea>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Stock Actual</label>
              <input type="number" name="stock_actual" id="edit_stock_actual" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Stock Mínimo</label>
              <input type="number" name="stock_minimo" id="edit_stock_minimo" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
            </div>
            <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Costo ($)</label>
              <input type="number" step="0.01" name="costo" id="edit_costo" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
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

  <script>
    const modal = document.getElementById('modalProduct');
    const btnOpen = document.getElementById('btnOpenModal');
    const closeBtns = document.querySelectorAll('.closeModal');
    const overlay = document.getElementById('overlay');
    const toggleModal = () => modal.classList.toggle('hidden');

    btnOpen.onclick = toggleModal;
    closeBtns.forEach(btn => btn.onclick = toggleModal);
    overlay.onclick = toggleModal;

    function openEditModal(data) {
        document.getElementById('edit_id').value = data.codigo_producto;
        document.getElementById('edit_nombre').value = data.nombre_producto;
        document.getElementById('edit_categoria').value = data.codigo_categoria;
        document.getElementById('edit_medida').value = data.codigo_medida;
        document.getElementById('edit_iva').value = data.codigo_iva;
        document.getElementById('edit_descripcion').value = data.descripcion;
        document.getElementById('edit_stock_actual').value = data.stock_actual;
        document.getElementById('edit_stock_minimo').value = data.stock_minimo;
        document.getElementById('edit_costo').value = data.costo;
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
