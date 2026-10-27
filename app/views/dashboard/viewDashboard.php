<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Panel Principal</title>
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

      <?php if (!empty($_SESSION['tasa_flash'])): ?>
        <?php $tasaFlash = $_SESSION['tasa_flash']; unset($_SESSION['tasa_flash']); ?>
        <div class="mb-6 rounded-lg border px-4 py-3 text-sm font-bold <?= ($tasaFlash['status'] ?? '') === 'error' ? 'border-red-200 bg-red-50 text-red-700' : 'border-green-200 bg-green-50 text-green-700' ?>">
          <?= htmlspecialchars($tasaFlash['message'] ?? '') ?>
        </div>
      <?php endif; ?>

      <div id="dashboardTasa" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <section class="bg-white rounded-custom border border-gray-100 shadow-sm p-6">
          <div class="flex flex-col gap-5">
            <div>
              <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Tasa del día</p>
              <p class="mt-1 text-3xl font-black text-navy-dark">
                Bs. <?= number_format((float) ($tasaActual['monto_bolivares'] ?? 0), 2, ',', '.') ?>
              </p>
              <p class="mt-1 text-xs font-bold text-gray-400">
                Última actualización: <?= !empty($tasaActual['fecha']) ? date('d/m/Y H:i', strtotime($tasaActual['fecha'])) : 'Sin registros' ?>
              </p>
            </div>
            <form method="POST" action="?url=dashboard" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <input type="hidden" name="guardar_tasa" value="1">
              <div>
                <label class="block text-xs font-black text-gray-400 uppercase mb-1">Tipo de moneda</label>
                <select name="codigo_moneda" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg outline-none font-black text-navy-dark focus:border-orange">
                  <?php foreach ($monedas as $moneda): ?>
                    <option value="<?= (int) $moneda['codigo_moneda'] ?>" <?= !empty($monedaActual) && (int) $monedaActual['codigo_moneda'] === (int) $moneda['codigo_moneda'] ? 'selected' : '' ?>><?= htmlspecialchars($moneda['nombre_moneda']) ?> (<?= htmlspecialchars($moneda['simbolo']) ?>)</option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div>
                <label class="block text-xs font-black text-gray-400 uppercase mb-1">Nueva tasa (Bs.)</label>
                <input type="number" name="monto_bolivares" min="0.01" step="0.01" required value="<?= htmlspecialchars(number_format((float) ($monedaActual['tasa_cambio'] ?? $tasaActual['monto_bolivares'] ?? 0), 2, '.', '')) ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg outline-none font-black text-navy-dark focus:border-orange">
              </div>
              <button type="submit" class="sm:col-span-2 px-5 py-2.5 bg-orange text-navy-dark rounded-lg font-black shadow-sm hover:bg-orange-dk transition-colors">GUARDAR TASA</button>
            </form>
          </div>
        </section>

        <section class="bg-white rounded-custom border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-black text-navy-dark uppercase tracking-widest">Historial reciente</h2>
          </div>
          <div class="divide-y divide-gray-100">
            <?php foreach ($historialTasas as $historialTasa): ?>
              <div class="px-5 py-3 flex items-center justify-between gap-3">
                <div>
                  <p class="text-xs font-black text-navy-dark uppercase"><?= htmlspecialchars($historialTasa['nombre_moneda'] ?? 'Moneda') ?> (<?= htmlspecialchars($historialTasa['simbolo'] ?? '') ?>)</p>
                  <p class="text-xs font-bold text-gray-400"><?= date('d/m/Y H:i', strtotime($historialTasa['fecha'])) ?></p>
                </div>
                <span class="font-black text-navy-dark whitespace-nowrap">Bs. <?= number_format((float) $historialTasa['monto_bolivares'], 2, ',', '.') ?></span>
              </div>
            <?php endforeach; ?>
            <?php if (empty($historialTasas)): ?>
              <p class="px-5 py-4 text-sm font-bold text-gray-400">No hay tasas registradas.</p>
            <?php endif; ?>
          </div>
        </section>
      </div>

      <div id="dashboardIndicadores" class="grid grid-cols-4 gap-6 mb-8">
        
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
  <script>
    const indicadores = document.getElementById('dashboardIndicadores');
    const tasa = document.getElementById('dashboardTasa');
    if (indicadores && tasa) tasa.before(indicadores);
  </script>
</body>
</html>
