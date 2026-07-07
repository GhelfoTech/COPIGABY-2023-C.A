<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Panel Principal</title>
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
          <div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden">
            <img src="assets/img/logo.jpeg" alt="Logo" class="w-full h-full object-cover">
          </div>
          <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter"><?= htmlspecialchars($nombreEmpresa ?? 'CopiGaby') ?> 2023</h2>
      </div>

      <div class="flex items-center">
        <?php include 'app/views/layouts/viewUsuarioHeader.php'; ?>
      </div>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <h1 class="text-2xl font-[900] text-gray-800 mb-8">Resumen de Actividad</h1>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-6 gap-6">
        
        <!-- Card: Productos Activos -->
        <div class="bg-white rounded-custom shadow-sm p-6 flex items-center justify-between border border-gray-100 hover:shadow-md transition-shadow">
          <div>
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Productos Activos</p>
            <h2 class="text-3xl font-black text-navy-dark mt-1"><?= $stats['productos_activos'] ?></h2>
          </div>
          <div class="bg-blue-50 text-blue-500 p-3 rounded-2xl">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
          </div>
        </div>

        <!-- Card: Servicios -->
        <div class="bg-white rounded-custom shadow-sm p-6 flex items-center justify-between border border-gray-100 hover:shadow-md transition-shadow">
          <div>
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Servicios Ofrecidos</p>
            <h2 class="text-3xl font-black text-navy-dark mt-1"><?= $stats['servicios_activos'] ?></h2>
          </div>
          <div class="bg-purple-50 text-purple-500 p-3 rounded-2xl">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          </div>
        </div>

        <!-- Card: Usuarios -->
        <div class="bg-white rounded-custom shadow-sm p-6 flex items-center justify-between border border-gray-100 hover:shadow-md transition-shadow">
          <div>
            <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Usuarios Activos</p>
            <h2 class="text-3xl font-black text-navy-dark mt-1"><?= $stats['usuarios_activos'] ?></h2>
          </div>
          <div class="bg-yellow-50 text-yellow-600 p-3 rounded-2xl">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          </div>
        </div>

        <!-- Card: Ventas del Mes -->
        <div class="bg-navy-dark rounded-custom shadow-lg p-6 flex items-center justify-between hover:-translate-y-1 transition-all">
          <div>
            <p class="text-xs font-black text-orange uppercase tracking-widest">Ingresos del Mes</p>
            <h2 class="text-3xl font-black text-white mt-1">$<?= number_format($stats['ventas_mes'], 2) ?></h2>
          </div>
          <div class="bg-white/10 text-orange p-3 rounded-2xl">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V3m0 9v3m0-3c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>

      </div>
    </main>
  </div>
</body>
</html>
