<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de IVA</title>
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
        <div class="w-10 h-10 bg-navy rounded-full border-2 border-orange flex items-center justify-center shrink-0">
          <img src="assets/img/logo.jpeg" alt="Logo" class="w-full h-full rounded-full object-cover">
        </div>
        <div class="font-['Pacifico'] text-white text-[1.15rem]">Copi<span class="text-orange">Gaby</span></div>
      </div>
    </div>
    <div class="p-[22px_20px_8px] text-[0.65rem] font-extrabold tracking-[2px] uppercase text-white/30">Menú Principal</div>
    
    <!-- Botón volver al Dashboard -->
    <a href="?url=dashboard" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] hover:bg-white/5 transition-all">
      <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
      Dashboard
    </a>

    <div class="dropdown-parent open mt-2">
      <button class="w-full flex items-center gap-3 px-5 py-[11px] text-white font-bold text-[0.88rem] border-l-[3px] border-orange bg-orange/10 dropdown-toggle">
        <svg class="w-[18px] h-[18px] text-orange" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
        Configuración
        <svg class="nav-arrow ml-auto w-[14px] rotate-90 transition-transform" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
      </button>
      <div class="dropdown-menu flex flex-col bg-black/20">
        <a href="?url=iva" class="pl-12 py-2 text-white text-[0.8rem] font-bold bg-white/5 transition-all">IVA</a>
        <a href="?url=usuario" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white transition-all">Usuario</a>
      </div>
    </div>
  </aside>

  <div class="ml-[260px] flex-1 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b-2 border-orange flex items-center justify-between px-8 sticky top-0 z-50 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
      <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter">Gestión de IVA</h2>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-[900] text-gray-800">Impuestos (IVA)</h1>
          <p class="text-gray-500 text-sm font-semibold">Configuración de porcentajes impositivos</p>
        </div>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          NUEVO IVA
        </button>
      </div>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
          <thead class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
            <tr>
              <th class="px-6 py-4">Código</th>
              <th class="px-6 py-4">Porcentaje</th>
              <th class="px-6 py-4">Fecha Registro</th>
              <th class="px-6 py-4">Estado</th>
              <th class="px-6 py-4 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y">
            <?php foreach ($ivas as $i): ?>
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4 font-black text-navy-dark">#<?= str_pad($i['codigo_IVA'], 3, '0', STR_PAD_LEFT) ?></td>
                <td class="px-6 py-4"><?= number_format($i['porcentaje_iva'], 2) ?>%</td>
                <td class="px-6 py-4 text-gray-500 font-bold"><?= date('d/m/Y', strtotime($i['fecha'])) ?></td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase <?= $i['estado'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                    <?= $i['estado'] ? 'Activo' : 'Inactivo' ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                   <div class="flex justify-center gap-2">
                    <button onclick='openEditModal(<?= json_encode($i) ?>)' class="text-blue-500 p-2 hover:bg-blue-50 rounded-lg transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="confirmDelete(<?= $i['codigo_IVA'] ?>)" class="text-red-500 p-2 hover:bg-red-50 rounded-lg transition-colors">
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

  <!-- MODAL REGISTRO -->
  <div id="modalIva" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="toggleModal()"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-md animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Nuevo IVA</h3>
          <button onclick="toggleModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=iva&type=register" method="POST" class="p-6">
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Porcentaje (%)</label>
            <input type="number" step="0.01" name="porcentaje_iva" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
          <div class="flex justify-end gap-3 mt-8">
            <button type="button" onclick="toggleModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL EDICIÓN -->
  <div id="modalEditIva" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-md animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Editar IVA</h3>
          <button onclick="closeEditModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=iva&type=update" method="POST" class="p-6">
          <input type="hidden" name="codigo_IVA" id="edit_codigo">
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Porcentaje (%)</label>
            <input type="number" step="0.01" name="porcentaje_iva" id="edit_porcentaje" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
          <div class="flex items-center gap-2 mb-4">
              <input type="checkbox" name="estado" id="edit_estado" class="w-4 h-4 accent-orange">
              <label class="text-sm font-bold text-navy-dark uppercase tracking-tight">Activo</label>
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
    // Sidebar Toggle logic
    document.querySelectorAll('.dropdown-toggle').forEach(btn => {
      btn.addEventListener('click', () => btn.parentElement.classList.toggle('open'));
    });

    // Modal Logic
    const modal = document.getElementById('modalIva');
    const btnOpen = document.getElementById('btnOpenModal');

    const toggleModal = () => modal.classList.toggle('hidden');
    btnOpen.onclick = toggleModal;

    function openEditModal(data) {
        document.getElementById('edit_codigo').value = data.codigo_IVA;
        document.getElementById('edit_porcentaje').value = data.porcentaje_iva;
        document.getElementById('edit_estado').checked = (data.estado == 1);
        document.getElementById('modalEditIva').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEditIva').classList.add('hidden');
    }

    function confirmDelete(id) {
      if(confirm('¿Desea desactivar este registro de IVA?')) {
        const f = new FormData();
        f.append('deleteIva', 'true');
        f.append('codigo_IVA', id);
        fetch('?url=iva&type=main', { method:'POST', body:f })
        .then(r => r.json()).then(d => {
          if(d.status === 'success') location.reload();
          else alert('No se pudo procesar');
        });
      }
    }
  </script>
</body>
</html>