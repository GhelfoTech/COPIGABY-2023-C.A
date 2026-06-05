<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Categorías</title>
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
      <h2 class="text-navy-dark font-extrabold text-xl">Gestión de Categorías</h2>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-[900] text-gray-800">Lista de Categorías</h1>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          NUEVA CATEGORÍA
        </button>
      </div>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
              <th class="px-6 py-4">Código</th>
              <th class="px-6 py-4">Nombre de Categoría</th>
              <th class="px-6 py-4">Estado</th>
              <th class="px-6 py-4 text-center">Acciones</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y">
            <?php if (!empty($categorias)): ?>
            <?php foreach ($categorias as $cat): ?>
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4">
                  <div class="font-black text-navy-dark">#<?= str_pad($cat['codigo_categoria'], 3, '0', STR_PAD_LEFT) ?></div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-navy-light uppercase font-bold"><?= htmlspecialchars($cat['nombre_categoria']) ?></div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase <?= $cat['estado'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' ?>">
                    <?= $cat['estado'] ? 'Activo' : 'Inactivo' ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-center flex justify-center gap-2">
                  <button onclick="openEditModal(<?= $cat['codigo_categoria'] ?>, '<?= htmlspecialchars($cat['nombre_categoria']) ?>', <?= $cat['estado'] ?>)" class="text-blue-400 hover:text-blue-600 p-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                  </button>
                  <button onclick="confirmDelete(<?= $cat['codigo_categoria'] ?>)" class="text-red-400 hover:text-red-600 p-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 font-bold italic">No se encontraron categorías registradas.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <div id="modalCategory" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" id="overlay"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-md animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark tracking-tight">Nueva Categoría</h3>
          <button class="text-gray-400 hover:text-navy-dark closeModal"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=categoria&type=register" method="POST" class="p-6 text-left">
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre de la Categoría</label>
            <input type="text" name="nombre_categoria" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-orange/20 focus:border-orange outline-none font-bold">
          </div>
          <div class="flex justify-end gap-3 mt-6">
            <button type="button" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg closeModal">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy transition-all">Guardar Categoría</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modalEditCategory" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-md animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark tracking-tight">Editar Categoría</h3>
          <button onclick="closeEditModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=categoria&type=update" method="POST" class="p-6 text-left">
          <input type="hidden" name="idcategoria" id="edit_id">
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre de la Categoría</label>
            <input type="text" name="nombre_categoria" id="edit_nombre" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
          <div class="mb-6 flex items-center gap-3">
             <input type="checkbox" name="estado" id="edit_estado" class="w-4 h-4 accent-orange">
             <span class="text-sm font-bold text-gray-700">Categoría Activa</span>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" onclick="closeEditModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy transition-all">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const modal = document.getElementById('modalCategory');
    const btnOpen = document.getElementById('btnOpenModal');
    const closeBtns = document.querySelectorAll('.closeModal');
    const overlay = document.getElementById('overlay');
    const toggleModal = () => modal.classList.toggle('hidden');

    btnOpen.onclick = toggleModal;
    closeBtns.forEach(btn => btn.onclick = toggleModal);
    overlay.onclick = toggleModal;

    function openEditModal(id, nombre, estado) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_nombre').value = nombre;
      document.getElementById('edit_estado').checked = (estado == 1);
      document.getElementById('modalEditCategory').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('modalEditCategory').classList.add('hidden');
    }

    function confirmDelete(id) {
      if(confirm('¿Desea desactivar esta categoría?')) {
        const f = new FormData();
        f.append('deleteCategory', 'true');
        f.append('idcategoria', id);
        fetch('?url=categoria&type=main', { method:'POST', body:f })
        .then(r => r.json()).then(d => {
          if(d.status === 'success') location.reload();
          else alert('No se pudo procesar');
        });
      }
    }
  </script>
</body>
</html>
