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
      <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter">Histórico de Compras</h2>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-[900] text-gray-800">Módulo de Compras</h1>
          <p class="text-gray-500 text-sm font-semibold">Registro de facturas recibidas de proveedores</p>
        </div>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          REGISTRAR FACTURA
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
              <th class="px-6 py-4 text-center">Acciones</th>
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
                    <?= $c['estado'] ? 'Completada' : 'Anulada' ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                   <?php if($c['estado']): ?>
                   <button onclick="confirmDelete(<?= $c['codigo_compra'] ?>)" class="text-red-500 p-2 hover:bg-red-50 rounded-lg transition-colors" title="Anular Compra">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                   </button>
                   <?php endif; ?>
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
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Nueva Compra a Proveedor</h3>
          <button onclick="toggleModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=compra&type=register" method="POST" class="p-6 grid grid-cols-2 gap-4">
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Proveedor</label>
            <select name="codigo_proveedor" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
              <?php foreach($proveedores as $p): ?>
                <option value="<?= $p['codigo_proveedor'] ?>"><?= $p['razon_social'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Factura #</label>
            <input type="text" name="numero_factura_proveedor" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold" maxlength="10" placeholder="Ej: F-1002">
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Fecha de Compra</label>
            <input type="date" name="fecha_compra" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold" value="<?= date('Y-m-d') ?>">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Monto Total de Factura ($)</label>
            <input type="number" step="0.01" name="monto_total" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-lg">
          </div>
          <div class="col-span-2 flex justify-end gap-3 mt-6">
            <button type="button" onclick="toggleModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">CANCELAR</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">REGISTRAR COMPRA</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const modal = document.getElementById('modalCompra');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => modal.classList.toggle('hidden');
    btnOpen.onclick = toggleModal;

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
  </script>
</body>
</html>
