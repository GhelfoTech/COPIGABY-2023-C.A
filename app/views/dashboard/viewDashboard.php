<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby 2026— Panel Principal</title>
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
