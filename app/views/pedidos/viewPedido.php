<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Gestión de Pedidos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/dist.css?v=3">
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <style>
    /* Las alertas de validación/registro de cliente siempre por delante de cualquier modal */
    .swal2-container { z-index: 20000 !important; }
  </style>
</head>
<body class="font-['Nunito'] bg-[#f0f2f7] text-[#1f2937] min-h-screen flex">

  <?php include 'app/views/layouts/viewMenuLateral.php'; ?>

  <div class="ml-[260px] flex-1 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b-2 border-orange flex items-center justify-between px-8 sticky top-0 z-50 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
      <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter">Gestión de Pedidos</h2>
      <div class="flex items-center">
        <?php include 'app/views/layouts/viewUsuarioHeader.php'; ?>
      </div>
    </header>

    <main id="pedidoConfig" class="p-8 flex-1 animate-fade-up" data-config="<?= htmlspecialchars(json_encode([
      'productos' => $productos,
      'servicios' => $servicios,
      'medidas' => $medidas,
      'tasaActual' => $tasaActual,
      'ivaDefaultCodigo' => (int) ($ivaActivo['codigo_IVA'] ?? 1),
      'simboloMoneda' => $monedaActiva['simbolo'] ?? '$',
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>">
      <?php if (!empty($pedidoFlash)): ?>
      <div id="pedidoFlash" class="mb-6 px-5 py-4 rounded-xl border font-bold text-sm <?= ($pedidoFlash['status'] ?? '') === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' ?>">
        <?= htmlspecialchars($pedidoFlash['message'] ?? 'Operación completada.') ?>
      </div>
      <?php endif; ?>

      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-[900] text-gray-800">Módulo de Pedidos</h1>
          <p class="text-gray-500 text-sm font-semibold">Registro de pedidos de productos y servicios</p>
          <?php if (!empty($monedaActiva)): ?>
          <p class="text-xs font-bold text-orange-dk mt-2">
            Moneda: <?= htmlspecialchars($monedaActiva['nombre_moneda']) ?> (<?= htmlspecialchars($monedaActiva['simbolo']) ?>)
            — Tasa: <?= number_format($tasaActual, 2) ?> Bs
          </p>
          <?php endif; ?>
        </div>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-gradient-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          AÑADIR PEDIDO
        </button>
      </div>

      <div class="bg-white rounded-custom shadow-sm overflow-hidden border border-gray-100">
        <table id="tablaPedidos" data-datatable="true" data-datatable-config="pedidos" class="w-full text-left">
          <thead class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
            <tr>
              <th class="px-6 py-4 text-center">ID</th>
              <th class="px-6 py-4">Cliente</th>
              <th class="px-6 py-4">Atendido por</th>
              <th class="px-6 py-4">Fecha</th>
              <th class="px-6 py-4 text-right">Monto Total</th>
              <th class="px-6 py-4">Método Pago</th>
              <th class="px-6 py-4 text-center">Estado</th>
              <th class="px-6 py-4 text-center">Consultar</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y"></tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- Modal Crear / Editar Pedido -->
  <div id="modalPedido" class="fixed inset-0 z-[150] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" id="overlayPedido"></div>
      <div class="relative bg-white shadow-xl rounded-custom w-full max-w-7xl animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/50">
          <h3 id="modalPedidoTitulo" class="text-xl font-black text-navy-dark tracking-tight">Nuevo Pedido</h3>
          <button type="button" class="text-gray-400 hover:text-navy-dark closeModalPedido">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>
        <form id="formPedido" action="?url=pedido&type=register" method="POST" class="p-6 text-left">
          <input type="hidden" name="items" id="itemsJson">
          <input type="hidden" name="codigo_pedido" id="codigoPedidoEdit" value="">
          <input type="hidden" name="ajax" value="1">
          <input type="hidden" name="subtotal" id="inputSubtotal" value="0.00">
          <input type="hidden" name="monto_iva" id="inputMontoIva" value="0.00">
          <input type="hidden" name="porcentaje_iva" id="inputPorcentajeIva" value="0.00">
          <input type="hidden" name="monto_total" id="inputMontoTotal" value="0.00">
          <input type="hidden" name="tasa_actual" id="inputTasaActual" value="<?= number_format($tasaActual, 2, '.', '') ?>">
          <input type="hidden" name="es_cashea" id="inputEsCashea" value="0">

           <div class="grid grid-cols-3 gap-4 mb-6">
             <div>
               <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Cliente <span class="text-red-500">*</span></label>
               <div class="flex gap-2">
                 <div class="relative min-w-0 flex-1">
                   <input type="search" id="buscarCliente" aria-label="Buscar cliente por nombre o cédula" placeholder="Escriba nombre o cédula" class="w-full px-3 py-2 bg-gray-50 border rounded-lg outline-none text-sm focus:border-orange">
                 </div>
                 <button type="button" id="btnNuevoClientePedido" title="Registrar un cliente nuevo" aria-label="Registrar un cliente nuevo" class="shrink-0 inline-flex items-center justify-center w-11 rounded-lg bg-orange text-navy-dark font-black text-2xl shadow-sm hover:bg-orange-dk transition-colors">+</button>
               </div>
               <select name="codigo_cliente" id="selectCliente" required class="hidden">
                  <option value="">— Seleccione un cliente —</option>
                  <?php foreach ($clientes as $cli): ?>
                    <option value="<?= htmlspecialchars((string) $cli['cedula_cliente']) ?>"><?= htmlspecialchars($cli['nombre']) ?> (<?= htmlspecialchars($cli['cedula_cliente']) ?>)</option>
                  <?php endforeach; ?>
               </select>
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Fecha del Pedido <span class="text-red-500">*</span></label>
              <input type="date" name="fecha_pedido" required max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" class="w-full px-4 py-2 bg-gray-50 border rounded-lg outline-none font-bold">
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Moneda / Tasa Global</label>
              <div class="w-full px-4 py-2 bg-orange/10 border border-orange/30 rounded-lg font-bold text-navy-dark">
                <span id="monedaActivaLabel"><?= htmlspecialchars($monedaActiva['nombre_moneda'] ?? '—') ?></span>
                <span class="text-orange-dk ml-1">(<?= htmlspecialchars($monedaActiva['simbolo'] ?? '$') ?>)</span>
                <span class="block text-xs text-gray-500 mt-1 font-semibold">
                  Tasa: <span id="tasaActualLabel"><?= number_format($tasaActual, 2) ?></span> Bs 
                </span>
              </div>
              <input type="hidden" id="tasaActualInput" value="<?= number_format($tasaActual, 2, '.', '') ?>">
            </div>
          </div>

          <div class="mb-4 flex justify-between items-center">
            <h4 class="text-sm font-black text-navy-dark uppercase tracking-wider">Detalle del Pedido</h4>
            <button type="button" id="btnAddItem" class="flex items-center gap-2 rounded-xl border border-orange/30 bg-orange/10 px-4 py-2.5 text-sm font-black uppercase text-orange-dk shadow-sm hover:bg-orange/20 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
              Agregar ítem
            </button>
          </div>

           <div class="overflow-x-auto border rounded-lg mb-4">
             <table class="w-full text-left text-sm">
                 <thead>
                  <tr class="bg-navy-dark text-white text-xs uppercase tracking-widest">
                    <th class="px-2 py-2 w-20">Tipo</th>
                    <th class="px-2 py-2 w-48">Descripción</th>
                     <th class="px-2 py-2 w-20">Unidad</th>
                     <th class="px-2 py-2 w-16">Cant</th>
                    <th class="px-2 py-2 w-20">Precio</th>
                    <th class="px-2 py-2 w-24 text-right">Subtotal</th>
                    <th class="px-2 py-2 w-10"></th>
                  </tr>
                </thead>
               <tbody id="itemsBody" class="divide-y font-semibold text-gray-700">
                 <tr id="emptyRow">
                   <td colspan="7" class="px-4 py-6 text-center text-gray-400 italic">Agregue al menos un producto o servicio</td>
                 </tr>
               </tbody>
            </table>
          </div>

          <div class="flex justify-end mb-6">
            <div class="bg-gray-50 border rounded-xl px-6 py-4 text-right min-w-[280px]">
              <div class="flex justify-between items-center text-sm mb-2">
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Subtotal</span>
                <span id="subtotalGeneral" class="font-bold text-navy-dark">$0.00</span>
              </div>
              <div class="flex justify-between items-center text-sm mb-3 pb-3 border-b border-gray-200 gap-3">
                <div class="flex items-center gap-2">
                  <span class="text-xs font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">IVA</span>
                  <select name="codigo_IVA" id="selectIva" required class="px-2 py-1 bg-white border rounded-lg text-xs font-bold focus:border-orange outline-none min-w-[110px]">
                    <?php if (!empty($ivas)): ?>
                      <?php foreach ($ivas as $iva): ?>
                        <option value="<?= (int) $iva['codigo_IVA'] ?>"
                          data-porcentaje="<?= number_format((float) $iva['porcentaje_iva'], 2, '.', '') ?>"
                          <?= ((int) $iva['codigo_IVA'] === (int) ($ivaActivo['codigo_IVA'] ?? 0)) ? 'selected' : '' ?>>
                          <?= number_format((float) $iva['porcentaje_iva'], 2) ?>%
                        </option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="<?= (int) ($ivaActivo['codigo_IVA'] ?? 1) ?>"
                        data-porcentaje="<?= number_format((float) ($ivaActivo['porcentaje_iva'] ?? 16), 2, '.', '') ?>">
                        <?= number_format((float) ($ivaActivo['porcentaje_iva'] ?? 16), 2) ?>%
                      </option>
                    <?php endif; ?>
                  </select>
                </div>
                <span id="montoIva" class="font-bold text-orange-dk">$0.00</span>
              </div>
              <span class="text-xs font-black text-gray-400 uppercase tracking-widest mr-4">Total General</span>
              <span id="totalGeneral" class="text-2xl font-black text-navy-dark">Bs 0.00</span>
              <div id="totalBs" class="text-xs font-bold text-orange-dk mt-1">$0.00</div>
            </div>
          </div>

          <!-- Sección de Pago -->
          <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-4 mb-6">
            <h4 class="text-sm font-black text-navy-dark uppercase tracking-wider mb-4">Datos del Pago</h4>
            <div class="grid grid-cols-3 gap-4">
              <div id="wrapMetodoPago">
                <label id="labelMetodoPago" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Método de Pago</label>
                <div class="relative">
                  <input type="search" id="buscarMetodo" aria-label="Buscar método de pago por nombre" placeholder="Escriba el método de pago" class="w-full px-3 py-2 bg-white border rounded-lg outline-none text-sm focus:border-orange">
                </div>
                <select name="codigo_metodo" id="selectMetodo" required class="hidden">
                  <option value="">— Seleccione —</option>
                  <?php foreach ($metodos as $m): ?>
                    <option value="<?= $m['codigo_metodo'] ?>"><?= htmlspecialchars($m['nombre_metodo']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div id="wrapBanco" class="hidden">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Banco</label>
                <select name="codigo_banco" id="selectBanco" class="w-full px-4 py-2 bg-white border rounded-lg focus:border-orange outline-none font-bold">
                  <option value="">— Seleccione banco —</option>
                  <?php foreach ($bancos as $b): ?>
                    <option value="<?= (int) $b['codigo_banco'] ?>"><?= sprintf('%04d', (int) $b['codigo_banco']) ?> - <?= htmlspecialchars($b['nombre_banco']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div id="wrapReferencia" class="hidden">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Referencia</label>
                <input type="text" name="referencia_pago" id="referenciaPago" maxlength="50" inputmode="numeric" pattern="[0-9]*" class="w-full px-4 py-2 bg-white border rounded-lg focus:border-orange outline-none font-bold" placeholder="N° de referencia">
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3">
            <button type="button" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg closeModalPedido">Cancelar</button>
            <button type="submit" id="btnSubmitPedido" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy transition-all">Registrar Pedido</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="casheaConfig" class="fixed inset-0 z-[170] hidden overflow-y-auto" aria-hidden="true">
    <div class="flex min-h-screen items-center justify-center p-4">
      <button type="button" class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" data-cashea-cancel aria-label="Cerrar configuración Cashea"></button>
      <section role="dialog" aria-modal="true" aria-labelledby="casheaModalTitle" class="relative w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">
        <header class="flex items-center justify-between border-b bg-gray-50/50 px-6 py-4">
          <h3 id="casheaModalTitle" class="text-xl font-black text-navy-dark">Añadir Cashea a este pedido</h3>
          <button type="button" data-cashea-cancel aria-label="Cerrar" class="text-gray-400 hover:text-navy-dark">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </header>
        <div class="space-y-4 p-6">
          <div>
            <label for="casheaPedidoActual" class="mb-1 block text-xs font-black uppercase text-gray-500">Pedido asociado</label>
            <input id="casheaPedidoActual" type="text" disabled value="Este pedido" class="w-full rounded-lg border bg-gray-100 px-4 py-2 text-sm font-bold text-navy-dark disabled:cursor-not-allowed disabled:opacity-100">
            <p id="casheaPedidoResumen" class="mt-1 text-xs font-semibold text-gray-500">El pedido actual se asociará automáticamente al guardar.</p>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label for="nivelCashea" class="mb-1 block text-xs font-black uppercase text-gray-500">Cantidad de cuotas</label>
              <select name="nivel_cashea" id="nivelCashea" class="w-full rounded-lg border bg-white px-3 py-2 font-bold outline-none focus:border-orange">
                <option value="">Seleccione cuotas</option>
                <?php for ($nivel = 1; $nivel <= 6; $nivel++): ?>
                  <option value="<?= $nivel ?>"><?= $nivel ?> cuotas</option>
                <?php endfor; ?>
              </select>
            </div>
            <div>
              <label for="porcentajeInicial" class="mb-1 block text-xs font-black uppercase text-gray-500">Porcentaje inicial</label>
              <select name="porcentaje_inicial" id="porcentajeInicial" class="w-full rounded-lg border bg-white px-3 py-2 font-bold outline-none focus:border-orange">
                <option value="">Seleccione inicial</option>
                <?php foreach ([60, 50, 40, 30, 20, 10, 0] as $porcentajeInicial): ?>
                  <option value="<?= $porcentajeInicial ?>"><?= $porcentajeInicial ?>%</option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <p class="text-xs font-semibold text-gray-500">El pedido y su total se tomarán automáticamente del formulario actual.</p>
          <div class="grid grid-cols-2 gap-3 rounded-xl border border-orange/30 bg-orange/5 p-4">
            <div class="rounded-lg bg-white px-3 py-2">
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Inicial</p>
              <p id="montoInicialCashea" class="mt-1 text-lg font-black text-navy-dark"><?= htmlspecialchars($monedaActiva['simbolo'] ?? '$') ?>0.00</p>
              <p id="montoInicialCasheaBs" class="text-xs font-semibold text-gray-500">Bs 0.00</p>
            </div>
            <div class="rounded-lg bg-white px-3 py-2">
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Saldo financiado</p>
              <p id="montoFinanciadoCashea" class="mt-1 text-lg font-black text-orange-dk"><?= htmlspecialchars($monedaActiva['simbolo'] ?? '$') ?>0.00</p>
              <p id="montoFinanciadoCasheaBs" class="text-xs font-semibold text-gray-500">Bs 0.00</p>
            </div>
          </div>
          <div class="flex justify-end gap-3 pt-2">
            <button type="button" data-cashea-cancel class="rounded-lg px-5 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100">Cancelar</button>
            <button type="button" id="btnConfirmarCashea" class="rounded-lg bg-navy-dark px-6 py-2 text-sm font-black text-white hover:bg-navy">Usar Cashea</button>
          </div>
        </div>
      </section>
    </div>
  </div>

  <?php $clienteModalAction = '?url=pedido&type=cliente'; include 'app/views/cliente/modalRegistroCliente.php'; unset($clienteModalAction); ?>

  <!-- Modal Ver Detalle (solo lectura) -->
  <div id="modalDetalle" class="fixed inset-0 z-[160] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/80 backdrop-blur-md" data-js-action="closeDetalleModal"></div>
      <div class="relative bg-white shadow-2xl rounded-custom w-full max-w-3xl animate-fade-up overflow-hidden">
        <div class="p-8">
          <div class="flex justify-between items-start border-b pb-6 mb-6">
            <div>
              <h3 class="text-2xl font-black text-navy-dark uppercase tracking-tighter" id="det_codigo">PEDIDO #0000</h3>
              <p class="text-gray-400 font-bold text-sm" id="det_fecha">Fecha: --/--/----</p>
            </div>
            <div class="text-right">
              <p class="text-xs font-black text-orange uppercase tracking-widest">Cliente</p>
              <h4 class="font-black text-navy-light uppercase" id="det_cliente">—</h4>
              <p class="text-gray-500 text-xs font-bold" id="det_telefono">Tel: —</p>
            </div>
          </div>

          <div class="mb-6">
            <table class="w-full text-left" id="tablaDetallesItems">
                 <thead class="bg-gray-50 text-gray-400 text-[0.65rem] uppercase font-black tracking-widest">
                   <tr>
                     <th class="px-4 py-3 text-navy-dark">Descripción</th>
                     <th class="px-4 py-3 text-center">Tipo</th>
                     <th class="px-4 py-3 text-center">Unidad</th>
                     <th class="px-4 py-3 text-center">Cant.</th>
                     <th class="px-4 py-3 text-right">Precio</th>
                     <th class="px-4 py-3 text-right">Subtotal</th>
                   </tr>
                 </thead>
              <tbody class="divide-y text-sm font-bold text-navy-dark"></tbody>
            </table>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 rounded-xl p-4 text-sm">
            <div>
              <p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Método de Pago</p>
              <p class="font-bold text-navy-dark" id="det_metodo">—</p>
            </div>
            <div>
              <p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Total Pagado</p>
              <p class="font-bold text-navy-dark" id="det_monto_pago">—</p>
            </div>
            <div>
              <p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">IVA Aplicado</p>
              <p class="font-bold text-orange-dk" id="det_iva">—</p>
            </div>
            <div id="det_banco_wrap" class="hidden">
              <p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Banco</p>
              <p class="font-bold text-navy-dark" id="det_banco">—</p>
            </div>
            <div id="det_ref_wrap" class="hidden">
              <p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Referencia</p>
              <p class="font-bold text-navy-dark" id="det_referencia">—</p>
            </div>
            <div id="det_cashea_wrap" class="col-span-2 hidden rounded-lg border border-orange/30 bg-orange/5 p-3">
              <p class="mb-2 text-[0.65rem] font-black uppercase tracking-widest text-orange-dk">Financiamiento Cashea</p>
              <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                <p class="text-xs font-bold text-gray-500">Nivel: <span id="det_cashea_nivel" class="text-navy-dark">—</span></p>
                <p class="text-xs font-bold text-gray-500">Inicial pagada: <span id="det_cashea_inicial" class="text-navy-dark">—</span></p>
                <p class="text-xs font-bold text-gray-500">Saldo financiado: <span id="det_cashea_saldo" class="text-navy-dark">—</span></p>
              </div>
            </div>
          </div>

          <div class="flex justify-between items-end border-t pt-6">
            <div>
              <p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Atendido por</p>
              <p class="text-navy-dark font-bold text-sm" id="det_usuario">—</p>
            </div>
            <div class="text-right">
              <p class="text-xs text-gray-400 font-bold mb-1">Subtotal: <span id="det_subtotal">$0.00</span></p>
              <span class="text-xs font-black text-gray-400 uppercase mr-4">Total del Pedido</span>
              <span class="text-3xl font-black text-navy-dark" id="det_total">$0.00</span>
              <div class="text-sm font-bold text-orange-dk mt-1" id="det_total_bs">0.00 Bs</div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-gray-50 px-8 py-4 flex justify-end gap-3 border-t">
          <button type="button" id="btnDetalleEliminar" class="px-5 py-2 text-sm font-black text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Eliminar</button>
          <button type="button" id="btnDetalleEditar" class="px-5 py-2 text-sm font-black text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">Modificar</button>
          <button type="button" data-js-action="closeDetalleModal" class="bg-navy-dark text-white font-black px-8 py-2 rounded-lg text-xs uppercase hover:bg-navy transition-all">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/js/searchable-select.js?v=<?= filemtime(__DIR__ . '/../../../assets/js/searchable-select.js') ?>"></script>
  <script src="assets/js/modules/pedido.js?v=<?= filemtime(__DIR__ . '/../../../assets/js/modules/pedido.js') ?>"></script>
  <script src="assets/js/datatables-init.js?v=4"></script>
</body>
</html>
                  