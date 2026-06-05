<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Compras</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy: { DEFAULT: '#1a2340', light: '#243050', dark: '#111827' },
            orange: { DEFAULT: '#f5a623', dk: '#d4891a' },
          },
          borderRadius: { 'custom': '14px' }
        }
      }
    }
  </script>
</head>
<body class="font-['Nunito'] bg-[#f0f2f7] text-[#1f2937] min-h-screen flex">

  <aside class="fixed inset-y-0 left-0 w-[260px] bg-navy-dark flex flex-col z-[100] shrink-0 sidebar-scroll overflow-y-auto">
    <div class="p-[20px_20px_16px] border-b border-white/10">
      <div class="flex items-center gap-[10px]">
        <div class="w-10 h-10 bg-navy rounded-full border-2 border-orange flex items-center justify-center shrink-0 shadow-[0_0_0_4px_rgba(245,166,35,0.1)]">
          <img src="assets/img/logo.jpeg" alt="Logo CopiGaby" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
        </div>
        <div>
          <div class="font-['Pacifico'] text-white text-[1.15rem] leading-tight">Copi<span class="text-orange">Gaby</span></div>
          <div class="text-[0.65rem] font-bold tracking-[2px] uppercase text-white/35">Sistema 2025</div>
        </div>
      </div>
    </div>
    <div class="p-[22px_20px_8px] text-[0.65rem] font-extrabold tracking-[2px] uppercase text-white/30">Menú Principal</div>
    
    <a href="?url=dashboard" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] hover:bg-white/5 transition-all">
      <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
      Dashboard
    </a>

    <a href="?url=compra" class="flex items-center gap-3 px-5 py-[11px] font-bold text-[0.88rem] border-l-[3px] border-orange bg-orange/10 text-white transition-all">
      <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4z" clip-rule="evenodd"/></svg>
      Compras
    </a>

    <a href="?url=producto" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] hover:bg-white/5 transition-all">
      <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
      Productos
    </a>

    <a href="?url=proveedor" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] hover:bg-white/5 transition-all">
      <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
      Proveedores
    </a>

    <div class="mt-auto p-4 border-t border-white/10">
      <a href="?url=login" class="flex items-center gap-[10px] text-white/45 font-bold text-[0.85rem] p-2 rounded-lg transition-all hover:text-red-300 hover:bg-red-500/10">
        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
        Cerrar Sesión
      </a>
    </div>
  </aside>

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

  <!-- MODAL REGISTRO -->
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