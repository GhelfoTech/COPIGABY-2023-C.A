<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Empresa</title>
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
      <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter">Configuración</h2>
      <div class="flex items-center">
        <?php include 'app/views/layouts/viewUsuarioHeader.php'; ?>
      </div>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-[900] text-gray-800">Datos de la Empresa</h1>
          <p class="text-gray-500 text-sm font-semibold">Gestione la información de su negocio</p>
        </div>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          AÑADIR EMPRESA
        </button>
      </div>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table data-datatable="true" class="w-full text-left">
          <thead class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
            <tr>
              <th class="px-6 py-4">RIF / Nombre</th>
              <th class="px-6 py-4">Contacto</th>
              <th class="px-6 py-4">Dirección</th>
              <th class="px-6 py-4 text-center">Logo</th>
              <th class="px-6 py-4 text-center">Consultar</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y">
            <?php if (!empty($empresas)): foreach ($empresas as $e): ?>
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4">
                  <div class="font-black text-navy-dark uppercase"><?= htmlspecialchars($e['nombre_empresa']) ?></div>
                  <div class="text-[0.7rem] text-gray-400 font-bold">RIF: <?= htmlspecialchars($e['rif_empresa']) ?></div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2 mb-1">
                    <svg class="w-3 h-3 text-orange" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 3z"/></svg>
                    <?= htmlspecialchars($e['telefono']) ?>
                  </div>
                  <div class="flex items-center gap-2 text-[0.75rem] text-gray-500">
                    <svg class="w-3 h-3 text-navy-light" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
                    <?= htmlspecialchars($e['correo']) ?>
                  </div>
                </td>
                <td class="px-6 py-4 text-[0.8rem] max-w-[200px] truncate" title="<?= htmlspecialchars($e['direccion']) ?>">
                  <?= htmlspecialchars($e['direccion']) ?>
                </td>
                <td class="px-6 py-4 text-center">
                  <?php if (!empty($e['logo'])): ?>
                    <img src="<?= htmlspecialchars($e['logo']) ?>" alt="Logo" class="h-8 w-8 object-contain mx-auto">
                  <?php else: ?>
                    <span class="text-gray-400 text-xs italic">Sin logo</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-center">
                   <button data-js-action="viewDetails" data-js-args="<?= htmlspecialchars(json_encode([(int) $e['codigo_empresa']], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>" class="group relative text-orange-dk p-2 hover:bg-orange/10 rounded-lg transition-colors">
                     <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Ver Detalle</span>
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                   </button>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400 font-bold italic">No hay empresas registradas.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <div id="modalEmpresa" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" data-js-action="toggleModal"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-md animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Nueva Empresa</h3>
          <button data-js-action="toggleModal" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
         <form action="?url=empresa&type=register" method="POST" class="p-6 grid grid-cols-2 gap-4">
           <div class="col-span-2">
             <label class="block text-xs font-black text-gray-400 uppercase mb-1">Nombre de la Empresa <span class="text-red-500">*</span></label>
             <input type="text" name="nombre_empresa" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold text-sm" placeholder="CopiGaby C.A.">
           </div>
           <div>
              <label class="block text-xs font-black text-gray-400 uppercase mb-1">RIF <span class="text-red-500">*</span></label>
              <div class="flex gap-0">
                <select name="rif_tipo" class="px-3 py-2 bg-gray-50 border border-r-0 rounded-l-lg text-sm font-black text-navy-dark outline-none focus:border-orange">
                  <option value="J-">J-</option>
                  <option value="V-">V-</option>
                </select>
                <input type="tel" inputmode="numeric" pattern="[0-9]{9}" maxlength="9" name="rif_numero" required class="w-full px-4 py-2 bg-gray-50 border rounded-r-lg focus:border-orange outline-none font-bold text-sm" placeholder="123456789">
                <input type="hidden" name="rif_empresa">
              </div>
            </div>
           <div>
              <label class="block text-xs font-black text-gray-400 uppercase mb-1">Teléfono <span class="text-red-500">*</span></label>
              <?php $telefonoFieldConfig = ['id' => 'telefonoEmpresaRegistro']; include 'app/views/layouts/telefonoField.php'; ?>
           </div>
           <div class="col-span-2">
              <label class="block text-xs font-black text-gray-400 uppercase mb-1">Correo Electrónico <span class="text-red-500">*</span></label>
             <input type="email" name="correo" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold" placeholder="contacto@empresa.com">
           </div>
           <div class="col-span-2">
             <label class="block text-xs font-black text-gray-400 uppercase mb-1">Dirección Física</label>
             <textarea name="direccion" rows="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold" placeholder="Calle Principal, Edificio X, Piso 1"></textarea>
           </div>
           <div class="col-span-2">
             <label class="block text-xs font-black text-gray-400 uppercase mb-1">URL del Logo (Opcional)</label>
             <input type="text" name="logo" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold" placeholder="assets/img/logo.png">
           </div>
           <div class="col-span-2 flex justify-end gap-3 mt-4">
             <button type="button" data-js-action="toggleModal" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
             <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Guardar</button>
           </div>
         </form>
      </div>
    </div>
  </div>

  <div id="modalEditEmpresa" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" data-js-action="closeEditModal"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-md animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Editar Empresa</h3>
          <button data-js-action="closeEditModal" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=empresa&type=update" method="POST" class="p-6 grid grid-cols-2 gap-4">
          <input type="hidden" name="codigo_empresa" id="edit_codigo">
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Nombre de la Empresa <span class="text-red-500">*</span></label>
            <input type="text" name="nombre_empresa" id="edit_nombre" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
           <div>
             <label class="block text-xs font-black text-gray-400 uppercase mb-1">RIF <span class="text-red-500">*</span></label>
             <div class="flex gap-0">
               <select name="rif_tipo" id="edit_rif_tipo" class="px-3 py-2 bg-gray-50 border border-r-0 rounded-l-lg text-sm font-black text-navy-dark outline-none focus:border-orange">
                 <option value="J-">J-</option>
                 <option value="V-">V-</option>
               </select>
               <input type="tel" inputmode="numeric" pattern="[0-9]{9}" maxlength="9" name="rif_numero" id="edit_rif" required class="w-full px-4 py-2 bg-gray-50 border rounded-r-lg focus:border-orange outline-none font-bold no-spinner">
               <input type="hidden" name="rif_empresa" id="edit_rif_completo">
             </div>
           </div>
          <div>
             <label class="block text-xs font-black text-gray-400 uppercase mb-1">Teléfono <span class="text-red-500">*</span></label>
             <?php $telefonoFieldConfig = ['id' => 'edit_telefono']; include 'app/views/layouts/telefonoField.php'; ?>
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Correo <span class="text-red-500">*</span></label>
            <input type="email" name="correo" id="edit_correo" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Dirección</label>
            <textarea name="direccion" id="edit_direccion" rows="2" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold"></textarea>
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">URL del Logo (Opcional)</label>
            <input type="text" name="logo" id="edit_logo" class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-none font-bold">
          </div>
          <div class="col-span-2 flex justify-end gap-3 mt-4">
            <button type="button" data-js-action="closeEditModal" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modalDetalle" class="fixed inset-0 z-[160] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/80 backdrop-blur-sm" data-js-action="closeDetalleModal"></div>
      <div class="relative bg-white shadow-2xl rounded-custom w-full max-w-md animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Detalle de Empresa</h3>
          <button type="button" data-js-action="closeDetalleModal" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6 space-y-4">
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Nombre</p><p id="det_nombre" class="font-black text-navy-dark uppercase">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">RIF</p><p id="det_rif" class="font-bold text-navy-dark">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Teléfono</p><p id="det_telefono" class="font-bold text-navy-dark">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Correo Electrónico</p><p id="det_correo" class="font-bold text-navy-dark">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Dirección Física</p><p id="det_direccion" class="font-bold text-navy-dark">—</p></div>
        </div>
        <div class="modal-footer bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
          <button type="button" id="btnDetalleEliminar" class="px-5 py-2 text-sm font-black text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Eliminar</button>
          <button type="button" id="btnDetalleEditar" class="px-5 py-2 text-sm font-black text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">Modificar</button>
          <button type="button" data-js-action="closeDetalleModal" class="px-6 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy transition-all">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/js/modules/empresa.js?v=<?= filemtime(__DIR__ . '/../../../assets/js/modules/empresa.js') ?>"></script>
  <script src="assets/js/datatables-init.js?v=4"></script>
</body>
</html>