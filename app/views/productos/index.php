<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Productos</title>
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

    <!-- Módulo: Pedido -->
    <div class="dropdown-parent">
      <button class="w-full flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] border-l-[3px] border-transparent transition-all hover:bg-white/5 hover:text-white hover:border-orange/50 group dropdown-toggle">
        <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/></svg>
        Pedido
        <svg class="nav-arrow ml-auto w-[14px] transition-transform" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
      </button>
      <div class="dropdown-menu flex flex-col bg-black/20">
        <a href="?url=pedidos" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white hover:bg-white/5 transition-all">Pedidos</a>
        <a href="?url=cliente" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white hover:bg-white/5 transition-all">Cliente</a>
      </div>
    </div>

    <a href="#" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] border-l-[3px] border-transparent transition-all hover:bg-white/5 hover:text-white hover:border-orange/50">
      <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4z" clip-rule="evenodd"/></svg>
      Compra
    </a>

    <!-- Módulo: Producto -->
    <div class="dropdown-parent open">
      <button class="w-full flex items-center gap-3 px-5 py-[11px] text-white font-bold text-[0.88rem] border-l-[3px] border-orange bg-orange/10 dropdown-toggle">
        <svg class="w-[18px] h-[18px] text-orange" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
        Producto
        <svg class="nav-arrow ml-auto w-[14px] transition-transform rotate-90" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
      </button>
      <div class="dropdown-menu flex flex-col bg-black/20">
        <a href="?url=producto" class="pl-12 py-2 text-white text-[0.8rem] font-bold transition-all bg-white/5">Productos</a>
        <a href="?url=categoria" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white transition-all">Categoría</a>
      </div>
    </div>

    <a href="#" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] border-l-[3px] border-transparent transition-all hover:bg-white/5 hover:text-white hover:border-orange/50">
      <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
      Servicios
    </a>

    <a href="#" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] border-l-[3px] border-transparent transition-all hover:bg-white/5 hover:text-white hover:border-orange/50">
      <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
      Proveedores
    </a>

    <!-- Módulo: Configuración -->
    <div class="dropdown-parent">
      <button class="w-full flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] border-l-[3px] border-transparent transition-all hover:bg-white/5 hover:text-white hover:border-orange/50 group dropdown-toggle">
        <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
        Configuración
        <svg class="nav-arrow ml-auto w-[14px] transition-transform" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
      </button>
      <div class="dropdown-menu flex flex-col bg-black/20">
        <a href="?url=usuario" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white hover:bg-white/5 transition-all">Usuario</a>
        <a href="?url=rol" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white hover:bg-white/5 transition-all">Rol</a>
        <a href="?url=metodopago" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white hover:bg-white/5 transition-all">Método de Pago</a>
        <a href="?url=moneda" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white hover:bg-white/5 transition-all">Moneda</a>
        <a href="?url=iva" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white hover:bg-white/5 transition-all">IVA</a>
        <a href="?url=empresa" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white hover:bg-white/5 transition-all">Empresa</a>
        <a href="?url=medida" class="pl-12 py-2 text-white/50 text-[0.8rem] font-bold hover:text-white hover:bg-white/5 transition-all">Unidad de Medida</a>
      </div>
    </div>

    <a href="#" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] border-l-[3px] border-transparent transition-all hover:bg-white/5 hover:text-white hover:border-orange/50">
      <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z" clip-rule="evenodd"/></svg>
      Reporte
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

  <!-- MODAL REGISTRO -->
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

  <!-- MODAL EDICIÓN -->
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
    // Sidebar Toggle
    document.querySelectorAll('.dropdown-toggle').forEach(btn => {
      btn.addEventListener('click', () => btn.parentElement.classList.toggle('open'));
    });

    // Modal Logic
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