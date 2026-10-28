<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Módulo Cashea</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/dist.css?v=4">
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="font-['Nunito'] bg-[#f0f2f7] text-[#1f2937] min-h-screen flex">

  <?php include 'app/views/layouts/viewMenuLateral.php'; ?>

  <div class="ml-[260px] flex-1 flex flex-col min-h-screen">
    <!-- Header Principal -->
    <header class="h-16 bg-white border-b-2 border-orange flex items-center justify-between px-8 sticky top-0 z-50 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
      <div class="flex items-center gap-3">
        <span class="inline-flex items-center justify-center bg-yellow-400 text-slate-950 font-black text-xs px-2.5 py-1 rounded-md tracking-wider uppercase shadow-xs">
          CASHEA
        </span>
        <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter">Módulo Transaccional Cashea</h2>
      </div>
      <div class="flex items-center">
        <?php include 'app/views/layouts/viewUsuarioHeader.php'; ?>
      </div>
    </header>

    <main class="p-8 flex-1 animate-fade-up">

      <!-- Mensajes Flash -->
      <?php if (!empty($casheaFlash)): ?>
      <div id="casheaFlashMsg" class="mb-6 px-5 py-4 rounded-xl border font-bold text-sm flex items-center justify-between <?= ($casheaFlash['status'] ?? '') === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' ?>">
        <div class="flex items-center gap-2">
          <?php if (($casheaFlash['status'] ?? '') === 'success'): ?>
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
          <?php else: ?>
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <?php endif; ?>
          <span><?= htmlspecialchars($casheaFlash['message'] ?? 'Operación realizada.') ?></span>
        </div>
        <button onclick="document.getElementById('casheaFlashMsg').remove()" class="text-gray-400 hover:text-gray-700">&times;</button>
      </div>
      <?php endif; ?>

      <!-- Header de Sección Cashea -->
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
          <div class="flex items-center gap-2 mb-1.5">
            <span class="inline-flex items-center gap-1 bg-yellow-400 text-slate-950 font-black text-xs px-2.5 py-0.5 rounded-md uppercase tracking-wider shadow-xs">
              <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>
              Cashea
            </span>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Financiamiento Quincenal</span>
          </div>
          <h1 class="text-2xl font-[900] text-navy-dark tracking-tight">Gestión de Financiamiento Cashea</h1>
          <p class="text-gray-500 text-sm font-semibold mt-0.5">Control transaccional de pagos iniciales, cronograma de cuotas (cada 14 días) y conciliación de depósitos</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
          <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-right">
            <p class="text-[0.65rem] uppercase tracking-wider text-gray-400 font-bold">Tasa Oficial</p>
            <p class="text-base font-black text-navy-dark">Bs. <?= number_format($tasaActual, 2) ?> <span class="text-xs text-gray-400">/ $</span></p>
          </div>
          <button type="button" onclick="openModalReglas()" class="flex items-center gap-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-navy-dark font-bold px-4 py-3 rounded-xl transition-all text-xs shadow-xs">
            <svg class="w-4 h-4 text-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Reglas y Niveles
          </button>
          <button type="button" id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-0.5 transition-all text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
            NUEVO CRÉDITO CASHEA
          </button>
        </div>
      </div>

      <!-- Tarjetas de Métricas (KPIs) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- 1. Total Financiado -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Financiado</p>
            <h3 class="text-2xl font-black text-navy-dark mt-1">$<?= number_format($estadisticas['total_financiado'], 2) ?></h3>
            <p class="text-[0.7rem] font-bold text-gray-400 mt-0.5">Bs. <?= number_format($estadisticas['total_financiado_bs'], 2) ?></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          </div>
        </div>

        <!-- 2. Total Recuperado / Liquidado -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Cobrado / Liquidado</p>
            <h3 class="text-2xl font-black text-emerald-600 mt-1">$<?= number_format($estadisticas['total_recuperado'], 2) ?></h3>
            <p class="text-[0.7rem] font-bold text-gray-400 mt-0.5">Bs. <?= number_format($estadisticas['total_recuperado_bs'], 2) ?></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>

        <!-- 3. Saldo Pendiente -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Por Cobrar (Pendiente)</p>
            <h3 class="text-2xl font-black text-orange-dk mt-1">$<?= number_format($estadisticas['total_pendiente'], 2) ?></h3>
            <p class="text-[0.7rem] font-bold text-gray-400 mt-0.5">Bs. <?= number_format($estadisticas['total_pendiente_bs'], 2) ?></p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-orange/10 text-orange-dk flex items-center justify-center font-black">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>

        <!-- 4. Cuotas en Mora -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
          <div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Créditos en Mora</p>
            <h3 class="text-2xl font-black <?= $estadisticas['en_mora'] > 0 ? 'text-red-600' : 'text-gray-700' ?> mt-1">
              <?= $estadisticas['en_mora'] ?>
            </h3>
            <p class="text-[0.7rem] font-bold text-gray-400 mt-0.5"><?= $estadisticas['activos'] ?> activos • <?= $estadisticas['cerrados'] ?> pagados</p>
          </div>
          <div class="w-12 h-12 rounded-xl <?= $estadisticas['en_mora'] > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-500' ?> flex items-center justify-center font-black">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          </div>
        </div>
      </div>

      <!-- Barra de Filtros y Búsqueda -->
      <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          </span>
          <input type="text" id="inputBuscar" oninput="filtrarTabla()" placeholder="Buscar por cliente, cédula, pedido o # crédito..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold outline-none focus:border-yellow-500 focus:bg-white transition-all">
        </div>

        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-gray-400 uppercase">Estado:</span>
          <button type="button" onclick="setFiltroEstado('todos', this)" class="btn-filtro px-3 py-1.5 rounded-lg text-xs font-black bg-navy-dark text-white transition-all">Todos</button>
          <button type="button" onclick="setFiltroEstado('activos', this)" class="btn-filtro px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">Activos</button>
          <button type="button" onclick="setFiltroEstado('mora', this)" class="btn-filtro px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">En Mora</button>
          <button type="button" onclick="setFiltroEstado('cerrados', this)" class="btn-filtro px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all">Pagados</button>
        </div>
      </div>

      <!-- Tabla Principal de Créditos Cashea -->
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
          <table class="w-full text-left" id="tablaCashea">
            <thead class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-wider">
              <tr>
                <th class="px-5 py-4">Crédito / Pedido</th>
                <th class="px-5 py-4">Cliente</th>
                <th class="px-5 py-4">Nivel Cashea</th>
                <th class="px-5 py-4">Inicial Pagada</th>
                <th class="px-5 py-4">Financiamiento</th>
                <th class="px-5 py-4">Progreso / Pendiente</th>
                <th class="px-5 py-4 text-center">Estado</th>
                <th class="px-5 py-4 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody class="text-gray-700 font-semibold text-sm divide-y divide-gray-100" id="tablaBody">
              <?php if (empty($cashea_creditos)): ?>
                <tr>
                  <td colspan="8" class="text-center py-12 text-gray-400">
                    <div class="flex flex-col items-center justify-center">
                      <div class="w-16 h-16 rounded-2xl bg-yellow-50 text-yellow-500 flex items-center justify-center mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      </div>
                      <p class="font-bold text-gray-600">No hay créditos Cashea registrados aún.</p>
                      <p class="text-xs text-gray-400 mt-1">Haz clic en "Nuevo Crédito Cashea" para financiar el primer pedido.</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php foreach ($cashea_creditos as $c): 
                  $esMora = !empty($c['tiene_mora']) && (int)$c['estado'] === 1;
                  $esCerrado = (int)$c['estado'] === 0 || $c['monto_pendiente'] <= 0;
                  $tipoFiltro = $esCerrado ? 'cerrados' : ($esMora ? 'mora' : 'activos');
                ?>
                  <tr class="hover:bg-slate-50/70 transition-colors fila-credito" 
                      data-tipo="<?= $tipoFiltro ?>"
                      data-texto="<?= strtolower(htmlspecialchars($c['id_cashea_credito'] . ' ' . $c['id_pedido'] . ' ' . ($c['nombre_cliente'] ?? '') . ' ' . ($c['cedula_cliente'] ?? ''))) ?>">
                    
                    <!-- ID Crédito & Pedido -->
                    <td class="px-5 py-4">
                      <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center bg-yellow-100 text-yellow-900 border border-yellow-300 text-[10px] font-black px-2 py-0.5 rounded uppercase">
                          C#<?= $c['id_cashea_credito'] ?>
                        </span>
                        <a href="?url=pedido" class="text-xs font-black text-navy-dark hover:text-orange transition-colors">
                          Pedido #<?= $c['id_pedido'] ?>
                        </a>
                      </div>
                      <div class="text-[0.7rem] text-gray-400 font-bold mt-1">
                        <?= date('d/m/Y H:i', strtotime($c['fecha'])) ?>
                      </div>
                    </td>

                    <!-- Cliente -->
                    <td class="px-5 py-4">
                      <div class="font-black text-navy-dark"><?= htmlspecialchars($c['nombre_cliente'] ?? 'Cliente General') ?></div>
                      <div class="text-xs text-gray-500 font-semibold">C.I: <?= htmlspecialchars($c['cedula_cliente'] ?? '—') ?></div>
                      <?php if (!empty($c['telefono_cliente'])): ?>
                        <div class="text-[0.7rem] text-gray-400 font-medium"><?= htmlspecialchars($c['telefono_cliente']) ?></div>
                      <?php endif; ?>
                    </td>

                    <!-- Nivel Cashea -->
                    <td class="px-5 py-4">
                      <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-black bg-amber-50 text-amber-900 border border-amber-300">
                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                        Nivel <?= $c['nivel_cliente'] ?>
                      </div>
                      <div class="text-[0.7rem] text-gray-500 font-bold mt-1">
                        <?= $c['num_cuotas'] ?> cuotas (<?= $c['porcentaje_financiado'] ?>%)
                      </div>
                    </td>

                    <!-- Inicial Pagada -->
                    <td class="px-5 py-4">
                      <div class="font-black text-emerald-600 text-sm">$<?= number_format($c['monto_inicial_pagado'], 2) ?></div>
                      <div class="text-[0.7rem] text-gray-400 font-semibold">Bs. <?= number_format($c['monto_inicial_bs'], 2) ?></div>
                    </td>

                    <!-- Financiamiento -->
                    <td class="px-5 py-4">
                      <div class="font-black text-navy-dark text-sm">$<?= number_format($c['monto_financiado'], 2) ?></div>
                      <div class="text-[0.7rem] text-gray-400 font-semibold">Bs. <?= number_format($c['monto_financiado_bs'], 2) ?></div>
                    </td>

                    <!-- Progreso / Pendiente -->
                    <td class="px-5 py-4 w-48">
                      <div class="flex items-center justify-between text-xs font-bold mb-1">
                        <span class="text-emerald-700 font-black">$<?= number_format($c['total_pagado'], 2) ?></span>
                        <span class="text-orange-dk font-black">$<?= number_format($c['monto_pendiente'], 2) ?></span>
                      </div>
                      <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden my-1">
                        <div class="h-2.5 rounded-full transition-all duration-500" style="width: <?= $c['porcentaje_progreso'] ?>%; background-color: #10b981;"></div>
                      </div>
                      <div class="text-[0.65rem] text-gray-400 font-bold text-right">
                        <?= $c['porcentaje_progreso'] ?>% saldado
                      </div>
                    </td>

                    <!-- Estado -->
                    <td class="px-5 py-4 text-center">
                      <?php if ($esCerrado): ?>
                        <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-[0.65rem] font-black px-2.5 py-1 rounded-full uppercase">
                          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                          Saldado
                        </span>
                      <?php elseif ($esMora): ?>
                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-[0.65rem] font-black px-2.5 py-1 rounded-full uppercase animate-pulse border border-red-200">
                          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                          En Mora (<?= $c['dias_mora_max'] ?>d)
                        </span>
                      <?php else: ?>
                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 border border-blue-200 text-[0.65rem] font-black px-2.5 py-1 rounded-full uppercase">
                          <svg class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                          Al Día
                        </span>
                      <?php endif; ?>
                    </td>

                    <!-- Acciones -->
                    <td class="px-5 py-4 text-center">
                      <div class="flex items-center justify-center gap-1.5">
                        <!-- Consultar / Cronograma -->
                        <button type="button" onclick="cargarDetalleCredito(<?= $c['id_cashea_credito'] ?>)" class="p-2 text-navy-dark hover:bg-navy-dark/10 rounded-lg transition-colors border border-gray-200 bg-white shadow-xs" title="Ver Expediente y Cronograma">
                          <svg class="w-4 h-4 text-navy-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>

                        <!-- Abonar / Liquidar -->
                        <?php if (!$esCerrado): ?>
                          <button type="button" onclick="openModalAbono(<?= $c['id_cashea_credito'] ?>, <?= $c['monto_pendiente'] ?>, <?= $c['num_cuotas'] ?>, <?= $c['monto_financiado'] ?>)" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors border border-emerald-200 bg-white shadow-xs" title="Registrar Abono / Pago de Cuota">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                          </button>
                        <?php endif; ?>

                        <!-- Eliminar / Anular (Admin) -->
                        <?php if (esAdmin()): ?>
                          <button type="button" onclick="confirmarEliminar(<?= $c['id_cashea_credito'] ?>, <?= $c['id_pedido'] ?>)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors border border-red-200 bg-white shadow-xs" title="Eliminar Crédito">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>

                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- ========================================== -->
  <!-- MODAL 1: REGISTRAR CRÉDITO CASHEA (SIMULADOR) -->
  <!-- ========================================== -->
  <div id="modalCashea" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
      <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" onclick="toggleModalCashea()"></div>
      <div class="relative bg-white shadow-2xl rounded-3xl w-full max-w-2xl animate-fade-up overflow-hidden border border-gray-100">
        
        <!-- Modal Header -->
        <div class="px-8 py-5 border-b bg-navy-dark text-white flex justify-between items-center">
          <div class="flex items-center gap-3">
            <span class="bg-yellow-400 text-slate-950 font-black text-xs px-2.5 py-1 rounded-md uppercase">Cashea</span>
            <h3 class="text-xl font-black">Nuevo Crédito Cashea</h3>
          </div>
          <button type="button" onclick="toggleModalCashea()" class="text-slate-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <!-- Formulario -->
        <form action="?url=cashea&type=register" method="POST" id="formRegistrarCashea" class="p-8 space-y-6">
          
          <!-- Selector de Pedido -->
          <div>
            <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">
              Seleccionar Pedido Elegible <span class="text-red-500">*</span>
            </label>
            <select name="id_pedido" id="selectPedido" required onchange="onPedidoSeleccionado()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-yellow-500 focus:bg-white outline-none font-bold text-sm text-navy-dark">
              <option value="">-- Elige un pedido para financiar --</option>
              <?php foreach ($pedidos_disponibles as $ped): ?>
                <option value="<?= $ped['codigo_pedido'] ?>" data-monto="<?= $ped['monto_total'] ?>" data-cliente="<?= htmlspecialchars($ped['nombre_cliente'] ?? 'Cliente General') ?>" data-cedula="<?= htmlspecialchars($ped['cedula_cliente']) ?>">
                  Pedido #<?= $ped['codigo_pedido'] ?> — <?= htmlspecialchars($ped['nombre_cliente'] ?? 'General') ?> ($<?= number_format($ped['monto_total'], 2) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <p class="text-[0.7rem] text-gray-400 mt-1 font-semibold">Solo se muestran pedidos activos que no tengan crédito Cashea.</p>
          </div>

          <!-- Total del Pedido & Selector de Nivel -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">Total Pedido ($ USD)</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 font-black text-gray-400">$</span>
                <input type="number" id="inputTotalPedido" step="0.01" min="0.01" required oninput="calcularCasheaSimulador()" placeholder="0.00" class="w-full pl-8 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl font-black text-navy-dark text-base outline-none">
              </div>
            </div>

            <div>
              <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-2">
                Nivel del Cliente Cashea <span class="text-red-500">*</span>
              </label>
              <select name="nivel_cliente" id="selectNivel" onchange="onNivelCambiado()" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-yellow-500 focus:bg-white outline-none font-black text-sm text-navy-dark">
                <?php foreach ($niveles_cashea as $niv => $regla): ?>
                  <option value="<?= $niv ?>" <?= $niv === 2 ? 'selected' : '' ?> data-inicial="<?= $regla['porcentaje_inicial'] ?>" data-financiado="<?= $regla['porcentaje_financiado'] ?>" data-cuotas='<?= json_encode($regla['cuotas_permitidas']) ?>'>
                    Nivel <?= $niv ?> (Inicial <?= $regla['porcentaje_inicial'] ?>% • Financia <?= $regla['porcentaje_financiado'] ?>%)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Desglose Porcentajes & Cuotas -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">% Inicial</label>
              <input type="number" id="inputPctInicial" min="1" max="99" value="40" oninput="onPctInicialManual()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl font-black text-sm text-navy-dark outline-none focus:border-yellow-500">
            </div>

            <div>
              <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">% Financiado</label>
              <input type="number" name="porcentaje_financiado" id="inputPctFinanciado" value="60" readonly class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl font-black text-sm text-gray-500 outline-none">
            </div>

            <div>
              <label class="block text-xs font-black text-gray-500 uppercase tracking-wider mb-1">Número de Cuotas</label>
              <select name="num_cuotas" id="selectCuotas" onchange="calcularCasheaSimulador()" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl font-black text-sm text-navy-dark outline-none focus:border-yellow-500">
                <option value="3">3 Cuotas (Estándar)</option>
                <option value="6">6 Cuotas</option>
                <option value="9">9 Cuotas</option>
                <option value="12">12 Cuotas</option>
              </select>
            </div>
          </div>

          <!-- Calculadora / Resultados de la Simulación en Vivo -->
          <div class="rounded-2xl p-5 text-white shadow-inner space-y-4" style="background-color: #111827; border: 1px solid #374151;">
            <div class="flex items-center justify-between border-b pb-3" style="border-color: #374151;">
              <span class="text-xs font-black uppercase tracking-wider text-yellow-400">Simulador de Pago Cashea</span>
              <span class="text-xs text-slate-300 font-semibold" id="simTasaTexto">Tasa: Bs. <?= number_format($tasaActual, 2) ?></span>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="rounded-xl p-3" style="background-color: #1f2937; border: 1px solid #374151;">
                <p class="text-[0.65rem] font-bold text-slate-400 uppercase">Inicial a Pagar Hoy</p>
                <p class="text-xl font-black text-emerald-400" id="simMontoInicial">$0.00</p>
                <p class="text-xs text-slate-400" id="simMontoInicialBs">Bs. 0.00</p>
                <input type="hidden" name="monto_inicial_pagado" id="inputMontoInicial">
              </div>

              <div class="rounded-xl p-3" style="background-color: #1f2937; border: 1px solid #374151;">
                <p class="text-[0.65rem] font-bold text-slate-400 uppercase">Monto a Financiar</p>
                <p class="text-xl font-black text-yellow-400" id="simMontoFinanciado">$0.00</p>
                <p class="text-xs text-slate-400" id="simMontoFinanciadoBs">Bs. 0.00</p>
                <input type="hidden" name="monto_financiado" id="inputMontoFinanciado">
              </div>
            </div>

            <!-- Previsualización de Cuotas Quincenales -->
            <div>
              <p class="text-[0.65rem] font-black uppercase text-slate-400 mb-2">Cronograma Estimado de Cuotas (Cada 14 días)</p>
              <div class="grid grid-cols-3 gap-2" id="simContenedorCuotas">
                <!-- Se llena dinámicamente -->
              </div>
            </div>
          </div>

          <!-- Botones de Acción -->
          <div class="flex justify-end gap-3 pt-2">
            <button type="button" onclick="toggleModalCashea()" class="px-6 py-2.5 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-xl transition-all">Cancelar</button>
            <button type="submit" class="px-8 py-2.5 text-sm font-black bg-gradient-to-r from-orange to-orange-dk text-navy-dark rounded-xl shadow-lg hover:-translate-y-0.5 transition-all">
              Registrar Crédito Cashea
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- MODAL 2: EXPEDIENTE Y CRONOGRAMA CASHEA   -->
  <!-- ========================================== -->
  <div id="modalDetalle" class="fixed inset-0 z-[160] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
      <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md" onclick="closeDetalleModal()"></div>
      <div class="relative bg-white shadow-2xl rounded-3xl w-full max-w-3xl animate-fade-up overflow-hidden border border-gray-100 max-h-[90vh] flex flex-col">
        
        <!-- Header -->
        <div class="px-8 py-5 border-b bg-navy-dark text-white flex justify-between items-center shrink-0">
          <div class="flex items-center gap-3">
            <span class="bg-yellow-400 text-slate-950 font-black text-xs px-2.5 py-1 rounded-md uppercase">Expediente Cashea</span>
            <h3 class="text-xl font-black text-white" id="detTitulo">Detalle de Crédito</h3>
          </div>
          <button type="button" onclick="closeDetalleModal()" class="text-slate-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <!-- Body Scrollable -->
        <div class="p-8 overflow-y-auto space-y-6 flex-1">
          
          <!-- Banner resumen -->
          <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Cliente</p>
              <p class="font-black text-navy-dark text-sm mt-0.5" id="detCliente">—</p>
              <p class="text-xs text-gray-500 font-semibold" id="detCedula">—</p>
            </div>
            <div>
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Pedido Asociado</p>
              <p class="font-black text-navy-dark text-sm mt-0.5" id="detPedido">—</p>
              <p class="text-xs text-gray-500 font-semibold" id="detTotalPedido">—</p>
            </div>
            <div>
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Inicial Pagada</p>
              <p class="font-black text-emerald-600 text-sm mt-0.5" id="detInicial">—</p>
              <p class="text-[0.7rem] text-gray-400" id="detInicialBs">—</p>
            </div>
            <div>
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Saldo Pendiente</p>
              <p class="font-black text-orange-dk text-sm mt-0.5" id="detPendiente">—</p>
              <p class="text-[0.7rem] text-gray-400" id="detPendienteBs">—</p>
            </div>
          </div>

          <!-- Barra de Progreso del Crédito -->
          <div>
            <div class="flex items-center justify-between text-xs font-bold mb-1.5">
              <span class="text-gray-500 font-black">Progreso del Financiamiento</span>
              <span class="text-navy-dark font-black" id="detProgresoTexto">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
              <div id="detProgresoBarra" class="h-3 rounded-full transition-all duration-500" style="width: 0%; background-color: #10b981;"></div>
            </div>
          </div>

          <!-- Cronograma de Cuotas Quincenales -->
          <div>
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-sm font-black text-navy-dark uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Cronograma de Cuotas (Cada 14 días)
              </h4>
              <span class="text-xs text-gray-400 font-bold" id="detNivelTexto">Nivel 2</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3" id="detContenedorCuotas">
              <!-- Renderizado dinámico de tarjetas de cuota -->
            </div>
          </div>

          <!-- Historial de Depósitos / Abonos Aplicados -->
          <div>
            <h4 class="text-sm font-black text-navy-dark uppercase tracking-wider mb-3 flex items-center gap-2">
              <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Depósitos y Abonos Registrados
            </h4>
            <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden" id="detTablaDepositosContainer">
              <table class="w-full text-left text-xs">
                <thead class="bg-gray-100 text-gray-500 font-black uppercase text-[0.65rem]">
                  <tr>
                    <th class="px-4 py-2.5">Fecha Depósito</th>
                    <th class="px-4 py-2.5">Monto Aplicado</th>
                    <th class="px-4 py-2.5">Referencia</th>
                    <th class="px-4 py-2.5">Nota / Concepto</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 font-semibold text-gray-700" id="detDepositosBody">
                  <!-- Se llena dinámicamente -->
                </tbody>
              </table>
            </div>
          </div>

        </div>

        <!-- Footer -->
        <div class="px-8 py-4 bg-gray-50 border-t flex justify-between items-center shrink-0">
          <div id="detBtnAbonarWrapper">
            <!-- Botón para abonar directo -->
          </div>
          <button type="button" onclick="closeDetalleModal()" class="px-6 py-2.5 text-sm font-black bg-navy-dark text-white rounded-xl hover:bg-slate-800 transition-all">
            Cerrar Expediente
          </button>
        </div>

      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- MODAL 3: REGISTRAR ABONO / DEPÓSITO        -->
  <!-- ========================================== -->
  <div id="modalAbono" class="fixed inset-0 z-[170] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
      <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" onclick="closeModalAbono()"></div>
      <div class="relative bg-white shadow-2xl rounded-3xl w-full max-w-md animate-fade-up overflow-hidden border border-gray-100">
        
        <!-- Header -->
        <div class="px-6 py-4 bg-emerald-600 text-white flex justify-between items-center">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <h3 class="text-lg font-black">Registrar Abono / Depósito</h3>
          </div>
          <button type="button" onclick="closeModalAbono()" class="text-emerald-100 hover:text-white transition-colors">&times;</button>
        </div>

        <!-- Form -->
        <form action="?url=cashea&type=deposit" method="POST" class="p-6 space-y-4">
          <input type="hidden" name="id_cashea_credito" id="abonoIdCredito">

          <!-- Saldo Pendiente Actual -->
          <div class="bg-orange/10 border border-orange/20 rounded-xl p-3 flex justify-between items-center">
            <span class="text-xs font-black uppercase text-orange-dk">Saldo Pendiente:</span>
            <span class="text-base font-black text-orange-dk" id="abonoSaldoPendienteTexto">$0.00</span>
          </div>

          <!-- Monto a Abonar -->
          <div>
            <div class="flex justify-between items-center mb-1">
              <label class="block text-xs font-black text-gray-500 uppercase">Monto a Abonar ($ USD) <span class="text-red-500">*</span></label>
              <button type="button" id="btnPagarCuotaExacta" class="text-[0.65rem] font-black text-emerald-600 hover:underline">Pagar Cuota Exacta</button>
            </div>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 font-black text-gray-400">$</span>
              <input type="number" step="0.01" min="0.01" name="monto_abono" id="inputMontoAbono" required class="w-full pl-8 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl font-black text-navy-dark outline-none focus:border-emerald-500">
            </div>
          </div>

          <!-- Referencia Bancaria / Liquidación -->
          <div>
            <label class="block text-xs font-black text-gray-500 uppercase mb-1">Referencia Bancaria / Comprobante</label>
            <input type="text" name="referencia" placeholder="Ej: 12345678 o Lote Cashea #45" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-sm text-navy-dark outline-none focus:border-emerald-500">
          </div>

          <!-- Fecha del Depósito -->
          <div>
            <label class="block text-xs font-black text-gray-500 uppercase mb-1">Fecha del Depósito</label>
            <input type="datetime-local" name="fecha_deposito" value="<?= date('Y-m-d\TH:i') ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-sm text-navy-dark outline-none focus:border-emerald-500">
          </div>

          <!-- Nota / Concepto -->
          <div>
            <label class="block text-xs font-black text-gray-500 uppercase mb-1">Nota o Concepto</label>
            <input type="text" name="nota" placeholder="Ej: Pago Cuota 1 o Liquidación Semanal" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-sm text-navy-dark outline-none focus:border-emerald-500">
          </div>

          <!-- Botones -->
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" onclick="closeModalAbono()" class="px-5 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-xl">Cancelar</button>
            <button type="submit" class="px-6 py-2 text-sm font-black bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-lg transition-all">
              Confirmar Abono
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- MODAL 4: REGLAS Y NIVELES CASHEA           -->
  <!-- ========================================== -->
  <div id="modalReglas" class="fixed inset-0 z-[180] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
      <div class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm" onclick="closeModalReglas()"></div>
      <div class="relative bg-white shadow-2xl rounded-3xl w-full max-w-2xl animate-fade-up overflow-hidden border border-gray-100 p-8">
        
        <div class="flex justify-between items-center mb-6">
          <div class="flex items-center gap-3">
            <span class="bg-yellow-400 text-slate-950 font-black text-xs px-2.5 py-1 rounded-md uppercase">Cashea</span>
            <h3 class="text-xl font-black text-navy-dark">Reglas y Niveles de Cashea</h3>
          </div>
          <button type="button" onclick="closeModalReglas()" class="text-gray-400 hover:text-gray-700">&times;</button>
        </div>

        <p class="text-sm text-gray-600 font-medium mb-4">
          Cashea es el sistema líder de financiamiento en Venezuela basado en cuotas quincenales (cada 14 días) sin interés. Según la puntualidad y nivel del usuario, varían sus beneficios:
        </p>

        <div class="space-y-3 max-h-[55vh] overflow-y-auto pr-1">
          <?php foreach ($niveles_cashea as $n => $r): ?>
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div>
                <div class="flex items-center gap-2">
                  <span class="bg-navy-dark text-yellow-400 text-xs font-black px-2.5 py-0.5 rounded">Nivel <?= $n ?></span>
                  <span class="font-black text-navy-dark text-sm"><?= $r['nombre'] ?></span>
                  <?php if ($n === 6): ?>
                    <span class="bg-amber-100 text-amber-800 border border-amber-300 text-[10px] font-black px-2 py-0.5 rounded uppercase">Rango VIP Máximo</span>
                  <?php endif; ?>
                </div>
                <p class="text-xs text-gray-600 font-bold mt-1"><?= $r['descripcion'] ?></p>
                <?php if (!empty($r['requisito'])): ?>
                  <p class="text-[0.7rem] text-gray-500 font-semibold mt-0.5">
                    <strong class="text-navy-dark">Requisito:</strong> <?= htmlspecialchars($r['requisito']) ?>
                  </p>
                <?php endif; ?>
              </div>
              <div class="text-right shrink-0">
                <span class="text-[0.65rem] font-bold text-gray-400 uppercase block">Inicial Mínima</span>
                <span class="text-base font-black text-emerald-600"><?= $r['porcentaje_inicial'] ?>%</span>
                <span class="text-[0.65rem] text-gray-400 block font-semibold"><?= implode(', ', $r['cuotas_permitidas']) ?> cuotas</span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-2xl flex items-start gap-3">
          <svg class="w-5 h-5 text-yellow-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <div class="text-xs text-yellow-900 font-semibold space-y-1">
            <p><strong class="font-black">Frecuencia de Pago:</strong> Todas las cuotas vencen cada 14 días (quincenal). Si un cliente no abona en su fecha límite, el estado pasará automáticamente a <span class="text-red-700 font-black">EN MORA</span>.</p>
            <p><strong class="font-black">Subir de Nivel:</strong> Se sube de nivel cumpliendo el número de cuotas pagadas a tiempo o el monto total acumulado en compras. Los puntos del Club Cashea se acumulan por pronto pago para canjear beneficios.</p>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <button type="button" onclick="closeModalReglas()" class="px-6 py-2.5 text-sm font-black bg-navy-dark text-white rounded-xl hover:bg-slate-800 transition-all">
            Entendido
          </button>
        </div>

      </div>
    </div>
  </div>

  <!-- SCRIPT LÓGICA INTERACTIVA CASHEA -->
  <script>
    const tasaActual = <?= json_encode((float)$tasaActual) ?>;
    const nivelesCashea = <?= json_encode($niveles_cashea) ?>;

    // Control de Modales
    const modalCashea = document.getElementById('modalCashea');
    const btnOpenModal = document.getElementById('btnOpenModal');
    const toggleModalCashea = () => modalCashea.classList.toggle('hidden');
    if (btnOpenModal) btnOpenModal.onclick = toggleModalCashea;

    function openModalReglas() { document.getElementById('modalReglas').classList.remove('hidden'); }
    function closeModalReglas() { document.getElementById('modalReglas').classList.add('hidden'); }

    function closeDetalleModal() { document.getElementById('modalDetalle').classList.add('hidden'); }
    function closeModalAbono() { document.getElementById('modalAbono').classList.add('hidden'); }

    // 1. Selector de Pedido
    function onPedidoSeleccionado() {
      const select = document.getElementById('selectPedido');
      const opt = select.options[select.selectedIndex];
      if (opt && opt.dataset.monto) {
        document.getElementById('inputTotalPedido').value = parseFloat(opt.dataset.monto).toFixed(2);
        calcularCasheaSimulador();
      }
    }

    // 2. Cambio de Nivel
    function onNivelCambiado() {
      const select = document.getElementById('selectNivel');
      const nivel = parseInt(select.value);
      const regla = nivelesCashea[nivel];

      if (regla) {
        document.getElementById('inputPctInicial').value = regla.porcentaje_inicial;
        document.getElementById('inputPctFinanciado').value = regla.porcentaje_financiado;

        // Actualizar opciones de cuotas
        const selectCuotas = document.getElementById('selectCuotas');
        selectCuotas.innerHTML = '';
        regla.cuotas_permitidas.forEach(c => {
          const opt = document.createElement('option');
          opt.value = c;
          opt.textContent = `${c} Cuotas (${c === 3 ? 'Estándar' : 'Línea Más Cuotas'})`;
          if (c === regla.cuotas_default) opt.selected = true;
          selectCuotas.appendChild(opt);
        });

        calcularCasheaSimulador();
      }
    }

    function onPctInicialManual() {
      let pctIni = parseFloat(document.getElementById('inputPctInicial').value) || 0;
      if (pctIni < 5) pctIni = 5;
      if (pctIni > 95) pctIni = 95;
      document.getElementById('inputPctFinanciado').value = Math.round(100 - pctIni);
      calcularCasheaSimulador();
    }

    // 3. Simulador de Cálculo en Tiempo Real
    function calcularCasheaSimulador() {
      const total = parseFloat(document.getElementById('inputTotalPedido').value) || 0;
      const pctIni = parseFloat(document.getElementById('inputPctInicial').value) || 40;
      const pctFin = 100 - pctIni;
      const numCuotas = parseInt(document.getElementById('selectCuotas').value) || 3;

      const montoInicial = Math.round((total * (pctIni / 100)) * 100) / 100;
      const montoFinanciado = Math.round((total - montoInicial) * 100) / 100;

      // Inputs ocultos para POST
      document.getElementById('inputMontoInicial').value = montoInicial.toFixed(2);
      document.getElementById('inputMontoFinanciado').value = montoFinanciado.toFixed(2);
      document.getElementById('inputPctFinanciado').value = pctFin;

      // Pantalla
      document.getElementById('simMontoInicial').textContent = '$' + montoInicial.toFixed(2);
      document.getElementById('simMontoInicialBs').textContent = 'Bs. ' + (montoInicial * tasaActual).toFixed(2);

      document.getElementById('simMontoFinanciado').textContent = '$' + montoFinanciado.toFixed(2);
      document.getElementById('simMontoFinanciadoBs').textContent = 'Bs. ' + (montoFinanciado * tasaActual).toFixed(2);

      // Render de tarjetas de cuotas estimadas
      const contenedor = document.getElementById('simContenedorCuotas');
      contenedor.innerHTML = '';

      if (montoFinanciado > 0) {
        const montoCuotaBase = Math.round((montoFinanciado / numCuotas) * 100) / 100;
        const hoy = new Date();

        for (let i = 1; i <= numCuotas; i++) {
          const montoEstaCuota = (i === numCuotas) 
            ? Math.round((montoFinanciado - (montoCuotaBase * (numCuotas - 1))) * 100) / 100
            : montoCuotaBase;

          const fechaCuota = new Date();
          fechaCuota.setDate(hoy.getDate() + (i * 14));
          const fechaStr = fechaCuota.toLocaleDateString('es-VE', { day: '2-digit', month: '2-digit', year: 'numeric' });

          const div = document.createElement('div');
          div.className = 'rounded-xl p-2.5 text-center';
          div.style.backgroundColor = '#1f2937';
          div.style.border = '1px solid #374151';
          div.innerHTML = `
            <div class="text-[0.65rem] font-black uppercase text-yellow-400">Cuota ${i}</div>
            <div class="text-xs font-bold text-slate-300 mt-0.5">${fechaStr}</div>
            <div class="text-sm font-black text-white mt-1">$${montoEstaCuota.toFixed(2)}</div>
            <div class="text-[0.65rem] text-slate-400">Bs. ${(montoEstaCuota * tasaActual).toFixed(2)}</div>
          `;
          contenedor.appendChild(div);
        }
      }
    }

    // Inicializar simulador con valores por defecto
    onNivelCambiado();

    // 4. Cargar Detalle de Crédito por AJAX
    function cargarDetalleCredito(id) {
      fetch(`?url=cashea&type=getDetail&id_cashea_credito=${id}`)
        .then(res => res.json())
        .then(res => {
          if (res.status === 'success' && res.data) {
            mostrarExpediente(res.data);
          } else {
            Swal.fire('Error', res.message || 'No se pudo cargar el detalle.', 'error');
          }
        })
        .catch(err => {
          Swal.fire('Error', 'Error de conexión al cargar el crédito.', 'error');
        });
    }

    function mostrarExpediente(data) {
      document.getElementById('detTitulo').textContent = `Expediente Crédito #${data.id_cashea_credito}`;
      document.getElementById('detCliente').textContent = data.nombre_cliente || 'Cliente General';
      document.getElementById('detCedula').textContent = `C.I: ${data.cedula_cliente || '—'} • ${data.telefono_cliente || ''}`;
      document.getElementById('detPedido').textContent = `Pedido #${data.id_pedido}`;
      document.getElementById('detTotalPedido').textContent = `Total: $${parseFloat(data.total_pedido).toFixed(2)}`;

      document.getElementById('detInicial').textContent = `$${parseFloat(data.monto_inicial_pagado).toFixed(2)}`;
      document.getElementById('detInicialBs').textContent = `Bs. ${(parseFloat(data.monto_inicial_pagado) * data.tasa_cambio).toFixed(2)}`;

      document.getElementById('detPendiente').textContent = `$${parseFloat(data.monto_pendiente).toFixed(2)}`;
      document.getElementById('detPendienteBs').textContent = `Bs. ${(parseFloat(data.monto_pendiente) * data.tasa_cambio).toFixed(2)}`;

      document.getElementById('detProgresoTexto').textContent = `${data.porcentaje_progreso}% saldado ($${data.total_pagado.toFixed(2)} de $${data.monto_financiado.toFixed(2)})`;
      document.getElementById('detProgresoBarra').style.width = `${data.porcentaje_progreso}%`;

      document.getElementById('detNivelTexto').textContent = `Nivel ${data.nivel_cliente} • ${data.num_cuotas} cuotas (${data.porcentaje_financiado}% financiado)`;

      // Render de Cronograma de Cuotas
      const contenedorCuotas = document.getElementById('detContenedorCuotas');
      contenedorCuotas.innerHTML = '';

      if (data.cronograma && data.cronograma.length > 0) {
        data.cronograma.forEach(cuota => {
          let badgeEstado = '';
          let bgColor = '#f8fafc';
          let borderColor = '#e2e8f0';

          if (cuota.estado === 'Pagada') {
            badgeEstado = `<span class="bg-emerald-100 text-emerald-800 text-[0.65rem] font-black px-2 py-0.5 rounded-full uppercase">Pagada</span>`;
            bgColor = '#f0fdf4';
            borderColor = '#86efac';
          } else if (cuota.estado === 'Vencida') {
            badgeEstado = `<span class="bg-red-100 text-red-700 text-[0.65rem] font-black px-2 py-0.5 rounded-full uppercase animate-pulse">Vencida (${cuota.dias_mora}d)</span>`;
            bgColor = '#fef2f2';
            borderColor = '#fca5a5';
          } else if (cuota.estado === 'Parcial') {
            badgeEstado = `<span class="bg-yellow-100 text-yellow-800 text-[0.65rem] font-black px-2 py-0.5 rounded-full uppercase">Parcial</span>`;
            bgColor = '#fefce8';
            borderColor = '#fde047';
          } else {
            badgeEstado = `<span class="bg-blue-100 text-blue-800 text-[0.65rem] font-black px-2 py-0.5 rounded-full uppercase">Pendiente (${cuota.dias_restantes}d)</span>`;
            bgColor = '#eff6ff';
            borderColor = '#bfdbfe';
          }

          const card = document.createElement('div');
          card.className = `p-3.5 rounded-2xl border flex flex-col justify-between`;
          card.style.backgroundColor = bgColor;
          card.style.borderColor = borderColor;
          card.innerHTML = `
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-black uppercase text-navy-dark">Cuota #${cuota.numero_cuota}</span>
              ${badgeEstado}
            </div>
            <div>
              <p class="text-[0.65rem] font-bold text-gray-500 uppercase">Vencimiento</p>
              <p class="text-xs font-black text-gray-800">${cuota.fecha_vencimiento}</p>
            </div>
            <div class="mt-2 pt-2 border-t border-gray-200/80 flex items-baseline justify-between">
              <span class="text-sm font-black text-navy-dark">$${parseFloat(cuota.monto_usd).toFixed(2)}</span>
              <span class="text-[0.7rem] font-bold text-gray-500">Bs. ${parseFloat(cuota.monto_bs).toFixed(2)}</span>
            </div>
          `;
          contenedorCuotas.appendChild(card);
        });
      }

      // Render de tabla de depósitos aplicados
      const tbody = document.getElementById('detDepositosBody');
      tbody.innerHTML = '';
      if (data.depositos && data.depositos.length > 0) {
        data.depositos.forEach(dep => {
          const tr = document.createElement('tr');
          tr.className = 'hover:bg-gray-100/70 transition-colors';
          tr.innerHTML = `
            <td class="px-4 py-2 font-bold text-gray-700">${dep.fecha_deposito}</td>
            <td class="px-4 py-2 font-black text-emerald-600">$${parseFloat(dep.monto_aplicado).toFixed(2)}</td>
            <td class="px-4 py-2 text-gray-500 font-medium">${dep.referencia || '—'}</td>
            <td class="px-4 py-2 text-gray-500 font-medium">${dep.nota || '—'}</td>
          `;
          tbody.appendChild(tr);
        });
      } else {
        tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-3 text-center text-gray-400 font-medium">Aún no se han registrado abonos ni liquidaciones para este crédito.</td></tr>`;
      }

      // Botón para abonar directo desde el modal de detalle
      const btnWrapper = document.getElementById('detBtnAbonarWrapper');
      btnWrapper.innerHTML = '';
      if (data.monto_pendiente > 0 && parseInt(data.estado) === 1) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'px-5 py-2.5 text-xs font-black bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow transition-all flex items-center gap-2';
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg> Abonar a este Crédito`;
        btn.onclick = () => {
          closeDetalleModal();
          openModalAbono(data.id_cashea_credito, data.monto_pendiente, data.num_cuotas, data.monto_financiado);
        };
        btnWrapper.appendChild(btn);
      }

      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    // 5. Modal de Abono
    function openModalAbono(idCredito, montoPendiente, numCuotas, montoFinanciado) {
      document.getElementById('abonoIdCredito').value = idCredito;
      document.getElementById('abonoSaldoPendienteTexto').textContent = '$' + parseFloat(montoPendiente).toFixed(2);
      
      const cuotaExacta = Math.round((montoFinanciado / (numCuotas || 3)) * 100) / 100;
      const cuotaSugerida = Math.min(cuotaExacta, montoPendiente);

      document.getElementById('inputMontoAbono').value = cuotaSugerida.toFixed(2);
      document.getElementById('inputMontoAbono').max = montoPendiente.toFixed(2);

      const btnCuota = document.getElementById('btnPagarCuotaExacta');
      btnCuota.onclick = () => {
        document.getElementById('inputMontoAbono').value = cuotaSugerida.toFixed(2);
      };

      document.getElementById('modalAbono').classList.remove('hidden');
    }

    // 6. Eliminar Crédito con Confirmación
    function confirmarEliminar(id, idPedido) {
      Swal.fire({
        title: '¿Eliminar crédito Cashea?',
        text: `Se anulará el crédito #${id} asociado al pedido #${idPedido}. Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          const formData = new FormData();
          formData.append('deleteCashea', '1');
          formData.append('id_cashea_credito', id);

          fetch('?url=cashea&type=delete', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(res => {
            if (res.status === 'success') {
              Swal.fire('Eliminado', res.message, 'success').then(() => location.reload());
            } else {
              Swal.fire('Error', res.message || 'No se pudo eliminar.', 'error');
            }
          })
          .catch(() => {
            Swal.fire('Error', 'Error de conexión con el servidor.', 'error');
          });
        }
      });
    }

    // 7. Filtros y Búsqueda en Vivo
    let filtroEstadoActual = 'todos';

    function setFiltroEstado(tipo, btn) {
      filtroEstadoActual = tipo;
      document.querySelectorAll('.btn-filtro').forEach(b => {
        b.className = 'btn-filtro px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all';
      });
      btn.className = 'btn-filtro px-3 py-1.5 rounded-lg text-xs font-black bg-navy-dark text-white transition-all';
      filtrarTabla();
    }

    function filtrarTabla() {
      const query = (document.getElementById('inputBuscar').value || '').toLowerCase().trim();
      const filas = document.querySelectorAll('.fila-credito');

      filas.forEach(f => {
        const texto = f.dataset.texto || '';
        const tipo = f.dataset.tipo || '';

        const coincideTexto = query === '' || texto.includes(query);
        const coincideEstado = filtroEstadoActual === 'todos' || tipo === filtroEstadoActual;

        if (coincideTexto && coincideEstado) {
          f.style.display = '';
        } else {
          f.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html>
