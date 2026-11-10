<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CopiGaby — Reportes</title>
  <link rel="stylesheet" href="assets/css/dist.css?v=4">
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="font-['Nunito'] bg-[#f0f2f7] text-[#1f2937] min-h-screen flex">
  <?php include 'app/views/layouts/viewMenuLateral.php'; ?>

  <div class="ml-[260px] flex-1 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b-2 border-orange flex items-center justify-between px-8 sticky top-0 z-50 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
      <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter">Reportes</h2>
      <div class="flex items-center"><?php include 'app/views/layouts/viewUsuarioHeader.php'; ?></div>
    </header>

    <main class="p-8 flex-1">
      <div class="mb-6">
        <h1 class="text-2xl font-black text-gray-800">Centro de reportes</h1>
        <p class="text-sm font-semibold text-gray-500">Consulta la información del negocio y descárgala en PDF o Word.</p>
      </div>

      <nav class="mb-6 flex flex-wrap gap-2" aria-label="Tipos de reporte">
        <?php foreach ($reportLabels as $key => $label): ?>
          <?php if ($key === 'compras' && !puedeVerModulo('compra')) { continue; } ?>
          <a href="?url=reporte&amp;report=<?= urlencode($key) ?>"
             class="rounded-xl px-4 py-3 text-sm font-extrabold transition <?= $reportType === $key ? 'bg-slate-900 text-white shadow' : 'bg-white text-slate-600 hover:bg-slate-100' ?>">
            <?= htmlspecialchars(str_replace('Reporte de ', '', $label)) ?>
          </a>
        <?php endforeach; ?>
      </nav>

      <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5">
          <h2 class="text-lg font-black text-slate-800"><?= htmlspecialchars($reportTypes[$reportType]) ?></h2>
          <p class="text-sm text-slate-500">
            <?php if ($reportType === 'pedidos'): ?>
              Elige los datos que quieres incluir y consulta los pedidos por fecha o por monto.
            <?php elseif ($reportType === 'inventario'): ?>
              Elige los datos que quieres incluir de las existencias de productos activos.
            <?php elseif ($reportType === 'faltantes'): ?>
              Elige los datos que quieres incluir de productos agotados o por debajo del stock mínimo.
            <?php else: ?>
              Elige los datos que quieres incluir de las compras registradas.
            <?php endif; ?>
          </p>
        </div>

        <form method="get" action="index.php" class="space-y-5">
          <input type="hidden" name="url" value="reporte">
          <input type="hidden" name="report" value="<?= htmlspecialchars($reportType) ?>">
          <?php if (in_array($reportType, ['pedidos', 'compras'], true)): ?>
          <div class="grid gap-4 md:grid-cols-3">
            <label class="text-sm font-bold text-slate-700">
              Período
              <select name="period" id="periodSelect" class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 font-semibold">
                <?php foreach (['all' => 'Todos', 'week' => 'Últimos 7 días', 'days15' => 'Últimos 15 días', 'month' => 'Último mes', 'custom' => 'Rango personalizado'] as $value => $label): ?>
                  <option value="<?= $value ?>" <?= $filterValues['period'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <label id="startDateWrap" class="text-sm font-bold text-slate-700">
              Desde
              <input type="date" name="start_date" value="<?= htmlspecialchars((string) $filterValues['start_date']) ?>" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2.5 font-semibold">
            </label>
            <label id="endDateWrap" class="text-sm font-bold text-slate-700">
              Hasta
              <input type="date" name="end_date" value="<?= htmlspecialchars((string) $filterValues['end_date']) ?>" class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2.5 font-semibold">
            </label>
          </div>
          <?php endif; ?>

          <div class="rounded-xl bg-slate-50 p-4">
            <h3 class="mb-3 text-sm font-black uppercase tracking-wide text-slate-700">Campos del reporte</h3>
            <input type="hidden" name="fields_present" value="1">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
              <?php foreach ($columns as $key => $label): ?>
                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                  <input type="checkbox" name="fields[]" value="<?= htmlspecialchars($key) ?>" <?= in_array($key, $selectedFields, true) ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                  <?= htmlspecialchars($label) ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <?php if ($reportType === 'pedidos'): ?>
          <div class="flex flex-wrap items-center gap-4 rounded-xl border border-slate-200 p-4">
            <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
              <input type="checkbox" name="largest" value="1" <?= $filterValues['largest'] ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500">
              Mostrar los pedidos de mayor monto
            </label>
            <label class="flex items-center gap-2 text-sm font-bold text-slate-700">
              Máximo
              <select name="top" class="rounded-lg border border-slate-300 bg-white px-3 py-2">
                <?php foreach ([5, 10, 20, 50] as $limit): ?>
                  <option value="<?= $limit ?>" <?= $filterValues['top'] === $limit ? 'selected' : '' ?>><?= $limit ?></option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>
          <?php endif; ?>

          <div class="flex flex-wrap items-center gap-3">
            <button type="submit" name="type" value="filter" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-black text-white hover:bg-slate-700">
              Generar reporte
            </button>
            <button type="submit" name="format" value="pdf" class="rounded-xl bg-red-600 px-5 py-3 text-sm font-black text-white hover:bg-red-700">
              Descargar PDF
            </button>
            <button type="submit" name="format" value="docx" class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white hover:bg-blue-800">
              Descargar DOCX
            </button>
          </div>
        </form>
      </section>

      <?php if ($errorMessage): ?>
        <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 font-bold text-red-700">
          <?= htmlspecialchars($errorMessage) ?>
        </div>
      <?php else: ?>
        <div class="mb-5 grid gap-6 sm:grid-cols-2">
          <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Registros</p>
            <p class="mt-1 text-2xl font-black text-slate-900"><?= number_format($summary['count'], 0, ',', '.') ?></p>
          </div>
          <?php if (isset($summary['total'])): ?>
          <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Monto total</p>
            <p class="mt-1 text-2xl font-black text-slate-900"><?= number_format($summary['total'], 2, ',', '.') ?></p>
          </div>
          <?php elseif (isset($summary['stock'])): ?>
          <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-black uppercase tracking-wider text-slate-500">Unidades en existencia</p>
            <p class="mt-1 text-2xl font-black text-slate-900"><?= number_format($summary['stock'], 0, ',', '.') ?></p>
          </div>
          <?php endif; ?>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-900 text-xs uppercase tracking-wider text-white">
              <tr>
                <?php foreach ($visibleColumns as $label): ?>
                  <th class="whitespace-nowrap px-4 py-4"><?= htmlspecialchars($label) ?></th>
                <?php endforeach; ?>
                <?php if (!$visibleColumns): ?>
                  <th class="px-4 py-4">Datos del reporte</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
              <?php if ($rows && $visibleColumns): ?>
                <?php foreach ($rows as $row): ?>
                <tr class="hover:bg-slate-50">
                  <?php foreach ($visibleColumns as $key => $_label): ?>
                    <?php
                      $value = $row[$key] ?? '—';
                      if (in_array($key, ['subtotal', 'monto_iva', 'monto_total', 'precio', 'valor_stock'], true)) {
                          $value = number_format((float) $value, 2, ',', '.');
                      } elseif ($key === 'porcentaje_iva') {
                          $value = number_format((float) $value, 2, ',', '.') . '%';
                      } elseif (in_array($key, ['fecha_pedido', 'fecha_compra'], true) && $value !== '') {
                          $value = date('d/m/Y H:i', strtotime((string) $value));
                      } elseif ($key === 'estado') {
                          $value = (int) $value === 1 ? 'Activo' : 'Anulado';
                      }
                    ?>
                    <td class="whitespace-nowrap px-4 py-3"><?= htmlspecialchars((string) $value) ?></td>
                  <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="<?= max(1, count($visibleColumns)) ?>" class="px-4 py-10 text-center font-semibold text-slate-500">
                  <?= $visibleColumns ? 'No hay registros para los filtros seleccionados.' : 'Selecciona al menos un campo para mostrar el reporte.' ?>
                </td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </main>
  </div>

  <script>
    (function () {
      var period = document.getElementById('periodSelect');
      var start = document.getElementById('startDateWrap');
      var end = document.getElementById('endDateWrap');
      if (!period || !start || !end) return;
      function updateDateInputs() {
        var show = period.value === 'custom';
        start.classList.toggle('hidden', !show);
        end.classList.toggle('hidden', !show);
      }
      period.addEventListener('change', updateDateInputs);
      updateDateInputs();
    })();
  </script>
</body>
</html>
