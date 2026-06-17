<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Monedas</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="assets/js/tailwind-config.js"></script>
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="font-['Nunito'] bg-[#f0f2f7] text-[#1f2937] min-h-screen flex">

  <?php include 'app/views/layouts/viewMenuLateral.php'; ?>

  <div class="ml-[260px] flex-1 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b-2 border-orange flex items-center justify-between px-8 sticky top-0 z-50 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
      <h2 class="text-navy-dark font-extrabold text-xl">Gestión de Monedas</h2>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <?php if (!empty($monedaFlash)): ?>
      <div class="mb-6 px-5 py-4 rounded-xl border font-bold text-sm <?= ($monedaFlash['status'] ?? '') === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
        <?= htmlspecialchars($monedaFlash['message'] ?? 'Operación completada.') ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($monedaActiva)): ?>
      <div class="mb-6 bg-gradient-to-r from-navy-dark to-navy text-white rounded-custom px-6 py-4 flex flex-wrap items-center justify-between gap-4 shadow-md">
        <div>
          <p class="text-[0.65rem] font-black uppercase tracking-widest text-orange/90 mb-1">Moneda global del sistema</p>
          <p class="text-xl font-black">
            <?= htmlspecialchars($monedaActiva['nombre_moneda']) ?>
            <span class="text-orange ml-2">(<?= htmlspecialchars($monedaActiva['simbolo']) ?>)</span>
          </p>
        </div>
        <div class="text-right">
          <p class="text-[0.65rem] font-black uppercase tracking-widest text-white/70">Tasa vigente</p>
          <p class="text-2xl font-black text-orange"><?= number_format((float) $monedaActiva['tasa_cambio'], 2) ?> Bs</p>
        </div>
      </div>
      <?php endif; ?>

      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-[900] text-gray-800">Divisas y Tasas</h1>
          <p class="text-gray-500 text-sm font-semibold">Configure la moneda de referencia para Pedidos, Compras y reportes</p>
        </div>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          AÑADIR MONEDA
        </button>
      </div>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
              <th class="px-6 py-4">Código</th>
              <th class="px-6 py-4">Nombre</th>
              <th class="px-6 py-4 text-center">Símbolo</th>
              <th class="px-6 py-4">Tasa (Bs)</th>
              <th class="px-6 py-4 text-center">Global</th>
              <th class="px-6 py-4">Estado</th>
              <th class="px-6 py-4 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y">
            <?php if (!empty($monedas)): foreach ($monedas as $m):
              $esGlobal = !empty($monedaActiva) && (int) $m['codigo_moneda'] === (int) $monedaActiva['codigo_moneda'];
            ?>
              <tr class="hover:bg-gray-50/80 transition-colors <?= $esGlobal ? 'bg-orange/5' : '' ?>">
                <td class="px-6 py-4">
                  <div class="font-black text-navy-dark">#<?= str_pad($m['codigo_moneda'], 3, '0', STR_PAD_LEFT) ?></div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-navy-light uppercase font-bold"><?= htmlspecialchars($m['nombre_moneda']) ?></div>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="bg-orange/10 text-orange-dk font-black px-2 py-1 rounded inline-block"><?= htmlspecialchars($m['simbolo']) ?></div>
                </td>
                <td class="px-6 py-4 font-black text-navy-dark">
                  <?= number_format((float) $m['tasa_cambio'], 2) ?>
                </td>
                <td class="px-6 py-4 text-center">
                  <?php if ($esGlobal): ?>
                    <span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase bg-orange text-navy-dark">Activa</span>
                  <?php elseif ((int) $m['estado'] === 1): ?>
                    <button type="button" onclick="activarMoneda(<?= (int) $m['codigo_moneda'] ?>, '<?= htmlspecialchars($m['nombre_moneda'], ENT_QUOTES) ?>')"
                      class="text-xs font-black uppercase text-navy-dark bg-gray-100 hover:bg-orange/20 px-3 py-1 rounded-full transition-colors">
                      Usar global
                    </button>
                  <?php else: ?>
                    <span class="text-gray-300 text-xs">—</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase <?= $m['estado'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                    <?= $m['estado'] ? 'Habilitada' : 'Inactiva' ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="flex justify-center gap-1">
                    <button type="button" onclick='openEditModal(<?= json_encode($m, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="group relative text-blue-400 hover:text-blue-600 p-2 transition-colors">
                      <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Modificar</span>
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <?php if (!$esGlobal): ?>
                    <button type="button" onclick="eliminar(<?= (int) $m['codigo_moneda'] ?>)" class="group relative text-red-400 hover:text-red-600 p-2 transition-colors">
                      <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-red-600 text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Desactivar</span>
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400 font-bold italic">No hay registros de monedas.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Modal Registro -->
  <div id="modalMoneda" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="toggleModal()"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-md animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark">Nueva Moneda</h3>
          <button type="button" onclick="toggleModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=moneda&type=register" method="POST" class="p-6">
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre</label>
            <input type="text" name="nombre_moneda" required maxlength="15" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold" placeholder="Ej: Dólar">
          </div>
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Símbolo</label>
            <input type="text" name="simbolo" required maxlength="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold" placeholder="Ej: $">
          </div>
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Tasa de Cambio (Bs)</label>
            <input type="number" step="0.01" min="0.01" name="tasa_cambio" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
          <div class="flex justify-end gap-3 mt-8">
            <button type="button" onclick="toggleModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Editar -->
  <div id="modalEditMoneda" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-md animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark">Editar Moneda</h3>
          <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=moneda&type=update" method="POST" class="p-6">
          <input type="hidden" name="idmoneda" id="edit_id">
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre</label>
            <input type="text" name="nombre_moneda" id="edit_nombre" required maxlength="15" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Símbolo</label>
            <input type="text" name="simbolo" id="edit_simbolo" required maxlength="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Tasa de Cambio (Bs)</label>
            <input type="number" step="0.01" min="0.01" name="tasa_cambio" id="edit_tasa" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
          <div class="mb-4 flex items-center gap-2">
            <input type="checkbox" name="estado" id="edit_estado" class="w-4 h-4 accent-orange">
            <label for="edit_estado" class="text-sm font-bold text-navy-dark uppercase">Habilitada</label>
          </div>
          <div class="flex justify-end gap-3 mt-8">
            <button type="button" onclick="closeEditModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const modal = document.getElementById('modalMoneda');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => modal.classList.toggle('hidden');
    btnOpen.onclick = toggleModal;

    function openEditModal(data) {
      document.getElementById('edit_id').value = data.codigo_moneda;
      document.getElementById('edit_nombre').value = data.nombre_moneda;
      document.getElementById('edit_simbolo').value = data.simbolo;
      document.getElementById('edit_tasa').value = data.tasa_cambio;
      document.getElementById('edit_estado').checked = (parseInt(data.estado, 10) === 1);
      document.getElementById('modalEditMoneda').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('modalEditMoneda').classList.add('hidden');
    }

    function activarMoneda(id, nombre) {
      Swal.fire({
        title: '¿Establecer moneda global?',
        text: `"${nombre}" se aplicará en Pedidos, Compras y conversiones del sistema.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, activar',
        cancelButtonText: 'Cancelar',
      }).then(result => {
        if (!result.isConfirmed) return;
        const f = new FormData();
        f.append('idmoneda', String(id));
        fetch('?url=moneda&type=setActive', { method: 'POST', body: f })
          .then(r => r.json())
          .then(d => {
            if (d.status === 'success') {
              Swal.fire({ icon: 'success', title: 'Moneda activa', text: d.message, timer: 1500, showConfirmButton: false })
                .then(() => location.reload());
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'No se pudo activar la moneda.' });
            }
          })
          .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación.' }));
      });
    }

    function eliminar(id) {
      Swal.fire({
        title: '¿Desactivar moneda?',
        text: 'La moneda quedará inactiva en el catálogo.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar',
      }).then(result => {
        if (!result.isConfirmed) return;
        const f = new FormData();
        f.append('deleteMoneda', 'true');
        f.append('idmoneda', String(id));
        fetch('?url=moneda&type=main', { method: 'POST', body: f })
          .then(r => r.json())
          .then(d => {
            if (d.status === 'success') {
              Swal.fire({ icon: 'success', title: 'Desactivada', timer: 1200, showConfirmButton: false })
                .then(() => location.reload());
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'No se pudo procesar.' });
            }
          })
          .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación.' }));
      });
    }
  </script>
</body>
</html>
