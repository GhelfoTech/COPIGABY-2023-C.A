<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Servicios</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/dist.css">
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
          <button data-js-action="abrirModalRegistro" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          AÑADIR SERVICIO
        </button>
      </div>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table id="tablaServicios" data-datatable="true" data-datatable-config="servicios" class="w-full text-left">
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
          <tbody class="text-gray-700 font-semibold text-sm divide-y"></tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Modal Registro -->
  <div id="modalRegister" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" data-js-action="toggleModal" data-js-args='["modalRegister"]'></div>
      <form action="?url=servicio&type=register" method="POST" class="relative bg-white shadow-xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark">Nuevo Servicio</h3>
          <button type="button" data-js-action="toggleModal" data-js-args='["modalRegister"]' class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6 space-y-4">
          <div>
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre <span class="text-red-500">*</span></label>
              <input type="text" name="nombre_servicio" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="Impresión a color">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
               <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Precio ($) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0.01" name="precio" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="5.00">
            </div>
          </div>
           <div>
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Descripción</label>
              <textarea name="descripcion" maxlength="50" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm h-24" placeholder="Incluye diseño básico y 100 unidades"></textarea>
           </div>

           <div class="border-t border-gray-100 pt-4 mt-2">
             <div class="flex items-center justify-between mb-2">
               <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Materiales / Insumos que consume</label>
               <span class="text-[0.65rem] font-bold text-gray-400 italic">Opcional</span>
             </div>
             <div class="flex flex-wrap md:flex-nowrap gap-2 items-stretch">
               <div class="flex-1 min-w-[200px]">
                 <div class="relative mb-1">
                   <input type="search" id="buscarProductoServicio_register" aria-label="Buscar producto o insumo por nombre" placeholder="Escriba el nombre del producto / insumo" class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:border-orange outline-none text-xs">
                 </div>
               <select id="selProducto_register" class="hidden">
                 <option value="">Seleccione un producto / insumo…</option>
                 <?php foreach ($productos as $p): ?>
                   <option value="<?= $p['codigo_producto'] ?>" data-nombre="<?= htmlspecialchars($p['nombre_producto']) ?>" data-stock="<?= (int)($p['stock_actual'] ?? 0) ?>">
                     <?= htmlspecialchars($p['nombre_producto']) ?> (Stock: <?= (int)($p['stock_actual'] ?? 0) ?>)
                   </option>
                 <?php endforeach; ?>
               </select>
               </div>
               <input type="number" id="cantProducto_register" min="1" step="1" value="1" class="w-28 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="Cantidad">
               <button type="button" data-js-action="agregarMaterial" data-js-args='["register"]' class="flex items-center gap-1 px-4 py-2 text-sm font-black bg-gradient-to-r from-orange to-orange-dk text-navy-dark rounded-lg hover:-translate-y-0.5 transition-all shadow-sm">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                 Agregar
               </button>
             </div>
             <div id="listaMateriales_register" class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
             <input type="hidden" name="materiales" id="materiales_register" value="[]">
           </div>

           <div class="flex justify-end gap-3 mt-8">
             <button type="button" data-js-action="toggleModal" data-js-args='["modalRegister"]' class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
             <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Guardar</button>
           </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Editar -->
  <div id="modalEdit" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" data-js-action="toggleModal" data-js-args='["modalEdit"]'></div>
      <form action="?url=servicio&type=update" method="POST" class="relative bg-white shadow-xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <input type="hidden" name="idservicio" id="edit_id">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark">Editar Servicio</h3>
          <button type="button" data-js-action="toggleModal" data-js-args='["modalEdit"]' class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6 space-y-4">
          <div>
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nombre <span class="text-red-500">*</span></label>
              <input type="text" name="nombre_servicio" id="edit_nombre" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="Impresión a color">
          </div>
          <div>
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Precio <span class="text-red-500">*</span></label>
             <input type="number" step="0.01" min="0.01" name="precio" id="edit_precio" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="5.00">
          </div>
           <div>
             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Descripción</label>
              <textarea name="descripcion" id="edit_descripcion" maxlength="50" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm h-24" placeholder="Incluye diseño básico y 100 unidades"></textarea>
           </div>

           <div class="border-t border-gray-100 pt-4 mt-2">
             <div class="flex items-center justify-between mb-2">
               <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Materiales / Insumos que consume</label>
               <span class="text-[0.65rem] font-bold text-gray-400 italic">Opcional</span>
             </div>
             <div class="flex flex-wrap md:flex-nowrap gap-2 items-stretch">
               <div class="flex-1 min-w-[200px]">
                 <div class="relative mb-1">
                   <input type="search" id="buscarProductoServicio_edit" aria-label="Buscar producto o insumo por nombre" placeholder="Escriba el nombre del producto / insumo" class="w-full px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:border-orange outline-none text-xs">
                 </div>
               <select id="selProducto_edit" class="hidden">
                 <option value="">Seleccione un producto / insumo…</option>
                 <?php foreach ($productos as $p): ?>
                   <option value="<?= $p['codigo_producto'] ?>" data-nombre="<?= htmlspecialchars($p['nombre_producto']) ?>" data-stock="<?= (int)($p['stock_actual'] ?? 0) ?>">
                     <?= htmlspecialchars($p['nombre_producto']) ?> (Stock: <?= (int)($p['stock_actual'] ?? 0) ?>)
                   </option>
                 <?php endforeach; ?>
               </select>
               </div>
               <input type="number" id="cantProducto_edit" min="1" step="1" value="1" class="w-28 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="Cantidad">
               <button type="button" data-js-action="agregarMaterial" data-js-args='["edit"]' class="flex items-center gap-1 px-4 py-2 text-sm font-black bg-gradient-to-r from-orange to-orange-dk text-navy-dark rounded-lg hover:-translate-y-0.5 transition-all shadow-sm">
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
             <button type="button" data-js-action="toggleModal" data-js-args='["modalEdit"]' class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
             <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Actualizar</button>
           </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Ver Detalle -->
  <div id="modalDetalle" class="fixed inset-0 z-[160] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/80 backdrop-blur-md" data-js-action="closeDetalleModal"></div>
      <div class="relative bg-white shadow-2xl rounded-custom w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 class="text-xl font-black text-navy-dark">Detalle del Servicio</h3>
          <button type="button" data-js-action="closeDetalleModal" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
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
          <button type="button" data-js-action="closeDetalleModal" class="px-6 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy transition-all">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/js/searchable-select.js?v=<?= filemtime(__DIR__ . '/../../../assets/js/searchable-select.js') ?>"></script>
  <script src="assets/js/modules/servicio.js?v=<?= filemtime(__DIR__ . '/../../../assets/js/modules/servicio.js') ?>"></script>
  <script src="assets/js/datatables-init.js?v=4"></script>
</body>
</html>