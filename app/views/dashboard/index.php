<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby 2026— Panel Principal</title>
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
    
    <a href="?url=dashboard" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] hover:bg-white/5 transition-all">
      <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
      Dashboard
    </a>

    <a href="?url=compra" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] border-l-[3px] border-transparent transition-all hover:bg-white/5 hover:text-white hover:border-orange/50">
      <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4z" clip-rule="evenodd"/></svg>
      Compra
    </a>

    <a href="?url=producto" class="flex items-center gap-3 px-5 py-[11px] text-white/60 font-bold text-[0.88rem] border-l-[3px] border-transparent transition-all hover:bg-white/5 hover:text-white hover:border-orange/50">
      <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
      Productos
    </a>
  </aside>

  <div class="ml-[260px] flex-1 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b-2 border-orange flex items-center justify-between px-8 sticky top-0 z-50 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-navy-dark rounded-full border-2 border-orange flex items-center justify-center overflow-hidden">
          <img src="assets/img/logo.jpeg" alt="Logo" class="w-full h-full object-cover">
        </div>
        <div class="font-['Pacifico'] text-navy-dark text-[1.1rem]">Copi<span class="text-orange">Gaby</span></div>
      </div>
      <div class="text-[0.85rem] font-extrabold text-navy-dark uppercase tracking-wide">
        <?= htmlspecialchars($_SESSION['username'] ?? 'Usuario') ?>
      </div>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <h1 class="text-[1.6rem] font-[900] text-gray-800 mb-6">Panel Principal</h1>

      <!-- Grid de Estadísticas Reales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-custom border-t-4 border-orange shadow-sm">
          <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">Productos</div>
          <div class="text-3xl font-black text-navy-dark"><?= $stats['productos_activos'] ?></div>
        </div>
        <div class="bg-white p-6 rounded-custom border-t-4 border-blue-500 shadow-sm">
          <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">Servicios</div>
          <div class="text-3xl font-black text-navy-dark"><?= $stats['servicios_activos'] ?></div>
        </div>
        <div class="bg-white p-6 rounded-custom border-t-4 border-green-500 shadow-sm">
          <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">Pedidos Mes</div>
          <div class="text-3xl font-black text-navy-dark"><?= $stats['pedidos_mes'] ?></div>
        </div>
        <div class="bg-white p-6 rounded-custom border-t-4 border-purple-500 shadow-sm">
          <div class="text-gray-400 text-xs font-black uppercase tracking-widest mb-2">Ventas</div>
          <div class="text-3xl font-black text-navy-dark">$<?= number_format($stats['ventas_mes'], 0) ?></div>
        </div>
      </div>

      <div class="bg-white rounded-custom p-8 border-l-8 border-orange shadow-sm">
        <h3 class="text-xl font-black text-navy-dark mb-2">¡Bienvenido al Sistema CopiGaby!</h3>
        <p class="text-gray-600 font-semibold mb-4">Usa el menú lateral para gestionar el inventario, proveedores y compras.</p>
        <div class="inline-block bg-navy text-orange px-4 py-2 rounded-full font-['Pacifico'] text-sm">¡Somos la Diferencia!</div>
      </div>
    </main>
  </div>
</body>
</html>