<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CopiGaby — Créditos Cashea</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Pacifico&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/dist.css">
  <link rel="stylesheet" href="assets/css/theme.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body class="font-[Nunito] bg-[#f0f2f7] text-[#1f2937] min-h-screen flex">

  <?php include 'app/views/layouts/viewMenuLateral.php'; ?>

  <div class="ml-[260px] flex-1 flex flex-col min-h-screen">
    <header class="h-16 bg-white border-b-2 border-orange flex items-center justify-between px-8 sticky top-0 z-50 shadow-[0_2px_12px_rgba(0,0,0,0.06)]">
      <h2 class="text-navy-dark font-extrabold text-xl uppercase tracking-tighter">Gestion de Cashea</h2>
      <div class="flex items-center">
        <?php include 'app/views/layouts/viewUsuarioHeader.php'; ?>
      </div>
    </header>

    <main class="p-8 flex-1 animate-fade-up">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl font-black text-gray-800">Cashea</h1>
          <p class="text-gray-500 text-sm font-semibold">Financiamiento de pedidos existentes</p>
        </div>
        <button id="btnOpenModal" class="flex items-center gap-2 bg-linear-to-r from-orange to-orange-dk text-navy-dark font-black px-6 py-3 rounded-xl shadow-lg hover:-translate-y-1 transition-all">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
          AÑADIR CASHEA
        </button>
      </div>

      <?php if (!empty($_SESSION['cashea_flash'])): ?>
        <?php $flash = $_SESSION['cashea_flash']; unset($_SESSION['cashea_flash']); ?>
        <script>
          showFlashAlert(
            <?= json_encode($flash['status'] ?? 'error', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
            <?= json_encode($flash['message'] ?? 'No se pudo completar la operación', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
          );
        </script>
      <?php endif; ?>

      <div class="bg-white rounded-[14px] shadow-sm overflow-hidden border border-gray-100">
        <table data-datatable="true" class="w-full text-left">
          <thead class="bg-navy-dark text-white text-[0.7rem] uppercase tracking-widest">
            <tr>
              <th class="px-6 py-4">Pedido</th>
              <th class="px-6 py-4">Cliente</th>
              <th class="px-6 py-4">Cuotas</th>
              <th class="px-6 py-4">Inicial</th>
              <th class="px-6 py-4">Financiado</th>
              <th class="px-6 py-4">Estado de Deuda</th>
              <th class="px-6 py-4 text-center">Consultar</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 font-semibold text-sm divide-y">
            <?php if (!empty($creditos)): foreach ($creditos as $c): ?>
              <?php $pendiente = (float) ($c['monto_pendiente'] ?? 0); ?>
              <tr class="hover:bg-gray-50/80 transition-colors">
                <td class="px-6 py-4 font-black text-navy-dark">#<?= (int) $c['id_pedido'] ?></td>
                <td class="px-6 py-4">
                  <div class="uppercase font-bold text-navy-light"><?= htmlspecialchars($c['nombre_cliente'] ?? '—') ?></div>
                  <div class="text-[0.7rem] text-gray-400 font-bold">V-<?= htmlspecialchars($c['cedula_cliente'] ?? '') ?></div>
                </td>
                <td class="px-6 py-4 font-black text-navy-dark">
                  <?php if ((int) ($c['cuotas_pendientes'] ?? 0) > 0): ?>
                    <?= (int) $c['cuotas_pendientes'] ?> / <?= (int) ($c['cantidad_cuotas'] ?? $c['nivel_cashea']) ?> Cuotas
                  <?php else: ?>
                    0 / <?= (int) ($c['cantidad_cuotas'] ?? $c['nivel_cashea']) ?> Cuotas
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">$<?= number_format((float) $c['monto_inicial_pagado'], 2, '.', ',') ?></td>
                <td class="px-6 py-4">$<?= number_format((float) $c['monto_financiado'], 2, '.', ',') ?></td>
                <td class="px-6 py-4">
                  <?php if ($pendiente > 0): ?>
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[0.65rem] bg-red-100 text-red-600 font-bold uppercase">
                      Con Deuda: $<?= number_format($pendiente, 2, '.', ',') ?>
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[0.65rem] bg-green-100 text-green-600 font-bold uppercase">
                      Solvente
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-center">
                  <button type="button" onclick='viewDetails(<?= json_encode($c, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="group relative text-orange-dk p-2 hover:bg-orange/10 rounded-lg transition-colors">
                    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Consultar</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                  </button>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <div id="modalCashea" class="fixed inset-0 z-150 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="toggleModal()"></div>
      <div class="relative bg-white shadow-xl rounded-[14px] w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Nuevo crédito Cashea</h3>
          <button type="button" onclick="toggleModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=cashea&type=register" method="POST" class="p-6" id="formCashea">
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Pedido existente <span class="text-red-500">*</span></label>
            <select name="id_pedido" id="selectPedido" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-hidden font-bold text-sm">
              <option value="">Seleccione un pedido</option>
              <?php if (!empty($pedidosDisponibles)): foreach ($pedidosDisponibles as $p): ?>
                <option value="<?= (int) $p['codigo_pedido'] ?>" data-total="<?= htmlspecialchars((string) $p['monto_total']) ?>">
                  Pedido #<?= (int) $p['codigo_pedido'] ?> — <?= htmlspecialchars($p['nombre_cliente'] ?? 'Cliente') ?> — $<?= number_format((float) $p['monto_total'], 2, '.', ',') ?>
                </option>
              <?php endforeach; endif; ?>
            </select>
            <p class="mt-1 text-[0.65rem] font-semibold text-gray-500">Solo se listan pedidos activos sin crédito Cashea vigente.</p>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase mb-1">Cantidad de Cuotas <span class="text-red-500">*</span></label>
              <input type="number" name="nivel_cashea" id="nivelCashea" min="1" value="1" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-hidden font-bold text-sm">
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase mb-1">Porcentaje inicial <span class="text-red-500">*</span></label>
              <select name="porcentaje_inicial" id="porcentajeInicial" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-hidden font-bold text-sm">
                <option value="">Seleccione inicial</option>
                <?php foreach ([0, 10, 20, 30, 40, 50, 60] as $porcentaje): ?>
                  <option value="<?= $porcentaje ?>"><?= $porcentaje ?>%</option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3 rounded-xl border border-orange/30 bg-orange/5 p-4 mb-4">
            <div class="rounded-lg bg-white px-3 py-2">
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Monto inicial</p>
              <p id="lblMontoInicial" class="mt-1 text-xl font-black text-navy-dark">$0.00</p>
            </div>
            <div class="rounded-lg bg-white px-3 py-2">
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Monto financiado</p>
              <p id="lblMontoFinanciado" class="mt-1 text-xl font-black text-orange-dk">$0.00</p>
            </div>
            <div class="col-span-2 rounded-lg bg-white px-3 py-2">
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Cuotas de referencia</p>
              <p id="lblCuotas" class="mt-1 text-sm font-bold text-navy-dark">Seleccione pedido, cuotas e inicial</p>
            </div>
          </div>
          <div class="flex justify-end gap-3 mt-4">
            <button type="button" onclick="toggleModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modalEditCashea" class="fixed inset-0 z-150 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="closeEditModal()"></div>
      <div class="relative bg-white shadow-xl rounded-[14px] w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Editar crédito Cashea</h3>
          <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=cashea&type=update" method="POST" class="p-6" id="formEditCashea">
          <input type="hidden" name="id_cashea_credito" id="edit_id">
          <input type="hidden" name="id_pedido" id="edit_id_pedido">
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Pedido</label>
            <input type="text" id="edit_pedido_label" readonly class="w-full px-4 py-2 bg-gray-100 border rounded-lg font-bold text-sm text-navy-dark">
          </div>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase mb-1">Cantidad de Cuotas <span class="text-red-500">*</span></label>
              <input type="number" name="nivel_cashea" id="edit_nivel" min="1" value="1" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-hidden font-bold text-sm">
            </div>
            <div>
              <label class="block text-xs font-black text-gray-400 uppercase mb-1">Porcentaje inicial <span class="text-red-500">*</span></label>
              <select name="porcentaje_inicial" id="edit_porcentaje" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-hidden font-bold text-sm">
                <?php foreach ([0, 10, 20, 30, 40, 50, 60] as $porcentaje): ?>
                  <option value="<?= $porcentaje ?>"><?= $porcentaje ?>%</option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3 rounded-xl border border-orange/30 bg-orange/5 p-4 mb-4">
            <div class="rounded-lg bg-white px-3 py-2">
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Monto inicial</p>
              <p id="editLblMontoInicial" class="mt-1 text-xl font-black text-navy-dark">$0.00</p>
            </div>
            <div class="rounded-lg bg-white px-3 py-2">
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Monto financiado</p>
              <p id="editLblMontoFinanciado" class="mt-1 text-xl font-black text-orange-dk">$0.00</p>
            </div>
            <div class="col-span-2 rounded-lg bg-white px-3 py-2">
              <p class="text-[0.65rem] font-black uppercase text-gray-400">Cuotas de referencia</p>
              <p id="editLblCuotas" class="mt-1 text-sm font-bold text-navy-dark">—</p>
            </div>
          </div>
          <div class="flex items-center gap-2 mb-4">
            <input type="checkbox" name="estado" id="edit_estado" class="w-4 h-4 accent-orange">
            <label class="text-sm font-bold text-navy-dark uppercase">Activo</label>
          </div>
          <div class="flex justify-end gap-3 mt-4">
            <button type="button" onclick="closeEditModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="modalDetalle" class="fixed inset-0 z-160 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/80 backdrop-blur-md" onclick="closeDetalleModal()"></div>
      <div class="relative bg-white shadow-2xl rounded-[14px] w-full max-w-md animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Detalle del crédito Cashea</h3>
          <button type="button" onclick="closeDetalleModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-6 space-y-4">
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Pedido</p><p id="det_pedido" class="font-black text-navy-dark">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Cliente</p><p id="det_cliente" class="font-bold text-navy-light uppercase">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Cantidad de Cuotas</p><p id="det_nivel" class="font-semibold text-gray-700">—</p></div>
          <div class="grid grid-cols-2 gap-4">
            <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Inicial</p><p id="det_inicial" class="font-black text-navy-dark">—</p></div>
            <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Financiado</p><p id="det_financiado" class="font-black text-orange-dk">—</p></div>
          </div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Estimado por cuota</p><p id="det_cuotas" class="font-semibold text-gray-700">—</p></div>
          <div id="det_cuotas_pendientes_wrap">
            <p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Cuotas pendientes</p>
            <p id="det_cuotas_pendientes" class="font-semibold text-gray-700">—</p>
          </div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Estado de deuda</p><p id="det_deuda" class="font-bold">—</p></div>
          <div><p class="text-[0.65rem] font-black text-gray-400 uppercase mb-1">Estado</p><p id="det_estado" class="font-bold">—</p></div>
        </div>
        <div class="modal-footer bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t">
          <?php if (esAdmin()): ?>
          <button type="button" id="btnDetalleEliminar" class="px-5 py-2 text-sm font-black text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Eliminar</button>
          <button type="button" id="btnDetalleEditar" class="px-5 py-2 text-sm font-black text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">Modificar</button>
          <?php endif; ?>
          <button type="button" id="btnDetalleAbonar" class="px-5 py-2 text-sm font-black text-green-600 bg-green-50 hover:bg-green-100 rounded-lg transition-colors hidden">Abonar</button>
          <button type="button" onclick="closeDetalleModal()" class="px-6 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy transition-all">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modalAbonarCashea" class="fixed inset-0 z-150 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="fixed inset-0 bg-navy-dark/60 backdrop-blur-sm" onclick="closeAbonarModal()"></div>
      <div class="relative bg-white shadow-xl rounded-[14px] w-full max-w-lg animate-fade-up overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50/50 flex justify-between items-center">
          <h3 class="text-xl font-black text-navy-dark">Abonar cuota Cashea</h3>
          <button type="button" onclick="closeAbonarModal()" class="text-gray-400 hover:text-navy-dark"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form action="?url=cashea&type=abonar" method="POST" class="p-6" id="formAbonarCashea">
          <input type="hidden" name="id_cashea_credito" id="abonar_id">
          <div class="mb-4 rounded-xl border border-orange/30 bg-orange/5 p-4">
            <p class="text-[0.65rem] font-black uppercase text-gray-400">Deuda actual</p>
            <p id="abonar_deuda_actual" class="mt-1 text-xl font-black text-navy-dark">$0.00</p>
            <p id="abonar_cuota_sugerida" class="mt-1 text-xs font-semibold text-gray-500">—</p>
          </div>
          <div class="mb-4">
            <label class="block text-xs font-black text-gray-400 uppercase mb-1">Monto a pagar <span class="text-red-500">*</span></label>
            <input type="number" step="0.01" min="0.01" name="monto_abonado" id="abonar_monto" required class="w-full px-4 py-2 bg-gray-50 border rounded-lg focus:border-orange outline-hidden font-bold text-sm">
          </div>
          <div class="flex justify-end gap-3 mt-4">
            <button type="button" onclick="closeAbonarModal()" class="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button type="submit" class="px-8 py-2 text-sm font-black bg-navy-dark text-white rounded-lg hover:bg-navy shadow-lg transition-all">Registrar pago</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const modal = document.getElementById('modalCashea');
    const btnOpen = document.getElementById('btnOpenModal');
    const selectPedido = document.getElementById('selectPedido');
    const nivelCashea = document.getElementById('nivelCashea');
    const porcentajeInicial = document.getElementById('porcentajeInicial');
    const formCashea = document.getElementById('formCashea');

    const toggleModal = () => {
      modal.classList.toggle('hidden');
      if (!modal.classList.contains('hidden')) {
        formCashea.reset();
        calcularCashea();
      }
    };
    btnOpen.onclick = toggleModal;

    function roundMoney(value) {
      const num = parseFloat(value);
      if (isNaN(num)) return 0;
      return Math.round((num + Number.EPSILON) * 100) / 100;
    }

    function toCents(value) {
      return Math.round((roundMoney(value) + Number.EPSILON) * 100);
    }

    function fromCents(cents) {
      return roundMoney(cents / 100);
    }

    function calcularCuotasPendientes(pendiente, financiado, cantidadCuotas) {
      cantidadCuotas = parseInt(cantidadCuotas, 10) || 0;
      if (cantidadCuotas < 1) return 0;
      const pendienteCents = toCents(pendiente);
      const financiadoCents = toCents(financiado);
      if (pendienteCents <= 0 || financiadoCents <= 0) return 0;
      if (pendienteCents >= financiadoCents) return cantidadCuotas;

      const cuotaBaseCents = Math.floor(financiadoCents / cantidadCuotas);
      const residuoCents = financiadoCents % cantidadCuotas;
      let restantePagado = financiadoCents - pendienteCents;
      let cuotasPagadas = 0;

      for (let i = 1; i <= cantidadCuotas; i++) {
        const montoCuotaCents = (i < cantidadCuotas)
          ? cuotaBaseCents
          : (cuotaBaseCents + residuoCents);
        if (montoCuotaCents <= 0) break;
        if (restantePagado >= montoCuotaCents) {
          restantePagado -= montoCuotaCents;
          cuotasPagadas++;
        } else {
          break;
        }
      }

      return cantidadCuotas - cuotasPagadas;
    }

    function calcularMontosCasheaJs(total, percent) {
      const totalCents = toCents(total);
      const inicialCents = Math.round(totalCents * (percent / 100));
      const financiadoCents = totalCents - inicialCents;
      return {
        inicial: fromCents(inicialCents),
        financiado: fromCents(financiadoCents)
      };
    }

    function formatMoney(value) {
      return '$' + roundMoney(value).toFixed(2);
    }

    function getMontoPedidoSeleccionado() {
      const option = selectPedido.options[selectPedido.selectedIndex];
      return option ? roundMoney(option.getAttribute('data-total') || '0') : 0;
    }

    function textoCuotas(nivel, financiado) {
      if (!nivel || nivel < 1 || financiado <= 0) {
        return 'Seleccione pedido, cuotas e inicial';
      }
      const montoCuota = roundMoney(financiado / nivel);
      return nivel + ' cuotas estimadas de ' + formatMoney(montoCuota);
    }

    function calcularCashea() {
      const total = getMontoPedidoSeleccionado();
      const percent = parseFloat(porcentajeInicial.value);
      const nivel = parseInt(nivelCashea.value, 10);
      const inicialValida = [0, 10, 20, 30, 40, 50, 60].includes(percent);
      const montos = inicialValida ? calcularMontosCasheaJs(total, percent) : { inicial: 0, financiado: 0 };
      const inicial = montos.inicial;
      const financiado = total > 0 && inicialValida ? montos.financiado : 0;

      document.getElementById('lblMontoInicial').textContent = formatMoney(inicial);
      document.getElementById('lblMontoFinanciado').textContent = formatMoney(financiado);
      document.getElementById('lblCuotas').textContent = textoCuotas(nivel, financiado);
    }

    nivelCashea.addEventListener('input', calcularCashea);
    selectPedido.addEventListener('change', calcularCashea);
    porcentajeInicial.addEventListener('change', calcularCashea);

    let currentRecord = null;
    let editTotal = 0;

    function viewDetails(data) {
      currentRecord = data;
      const pendiente = parseFloat(data.monto_pendiente || 0);
      document.getElementById('det_pedido').textContent = '#' + data.id_pedido + ' · Total ' + formatMoney(data.monto_total);
      document.getElementById('det_cliente').textContent = data.nombre_cliente || '—';
      document.getElementById('det_nivel').textContent = data.nivel_cashea + ' Cuotas';
      document.getElementById('det_inicial').textContent = formatMoney(data.monto_inicial_pagado) + ' (' + data.porcentaje_inicial + '%)';
      document.getElementById('det_financiado').textContent = formatMoney(data.monto_financiado);
      document.getElementById('det_cuotas').textContent = textoCuotas(parseInt(data.nivel_cashea, 10), parseFloat(data.monto_financiado || 0));
      document.getElementById('det_deuda').innerHTML = pendiente > 0
        ? '<span class="bg-red-100 text-red-600 font-bold px-3 py-1 rounded-full text-[0.65rem] uppercase">Con Deuda: ' + formatMoney(pendiente) + '</span>'
        : '<span class="bg-green-100 text-green-600 font-bold px-3 py-1 rounded-full text-[0.65rem] uppercase">Solvente</span>';
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      const wrapPendientes = document.getElementById('det_cuotas_pendientes_wrap');
      const lblPendientes = document.getElementById('det_cuotas_pendientes');
      const cantidadCuotas = parseInt(data.cantidad_cuotas || data.nivel_cashea, 10);
      const cuotasPendientes = (typeof data.cuotas_pendientes !== 'undefined' && data.cuotas_pendientes !== null)
        ? parseInt(data.cuotas_pendientes, 10)
        : calcularCuotasPendientes(pendiente, data.monto_financiado || 0, cantidadCuotas);
      if (pendiente > 0 && cuotasPendientes > 0) {
        lblPendientes.textContent = cuotasPendientes + (cuotasPendientes === 1 ? ' cuota pendiente' : ' cuotas pendientes');
        wrapPendientes.classList.remove('hidden');
      } else {
        lblPendientes.textContent = 'Sin cuotas pendientes';
        wrapPendientes.classList.add('hidden');
      }
      const btnEditar = document.getElementById('btnDetalleEditar');
      const btnEliminar = document.getElementById('btnDetalleEliminar');
      const btnAbonar = document.getElementById('btnDetalleAbonar');
      if (btnEditar) btnEditar.onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      if (btnEliminar) btnEliminar.onclick = () => confirmDelete(currentRecord.id_cashea_credito);
      if (btnAbonar) {
        if (pendiente > 0) {
          btnAbonar.classList.remove('hidden');
          btnAbonar.onclick = () => openAbonarModal(currentRecord);
        } else {
          btnAbonar.classList.add('hidden');
          btnAbonar.onclick = null;
        }
      }
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function calcularEditCashea() {
      const percent = parseFloat(document.getElementById('edit_porcentaje').value);
      const nivel = parseInt(document.getElementById('edit_nivel').value, 10);
      const inicialValida = [0, 10, 20, 30, 40, 50, 60].includes(percent);
      const montos = inicialValida ? calcularMontosCasheaJs(editTotal, percent) : { inicial: 0, financiado: 0 };
      const inicial = montos.inicial;
      const financiado = editTotal > 0 && inicialValida ? montos.financiado : 0;
      document.getElementById('editLblMontoInicial').textContent = formatMoney(inicial);
      document.getElementById('editLblMontoFinanciado').textContent = formatMoney(financiado);
      document.getElementById('editLblCuotas').textContent = textoCuotas(nivel, financiado);
    }

    function openEditModal(data) {
      editTotal = roundMoney(data.monto_total || 0);
      document.getElementById('edit_id').value = data.id_cashea_credito;
      document.getElementById('edit_id_pedido').value = data.id_pedido;
      document.getElementById('edit_pedido_label').value = 'Pedido #' + data.id_pedido + ' — ' + formatMoney(data.monto_total);
      document.getElementById('edit_nivel').value = data.nivel_cashea;
      document.getElementById('edit_porcentaje').value = String(data.porcentaje_inicial);
      document.getElementById('edit_estado').checked = data.estado == 1;
      calcularEditCashea();
      document.getElementById('modalEditCashea').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('modalEditCashea').classList.add('hidden');
    }

    function openAbonarModal(data) {
      closeDetalleModal();
      const pendiente = roundMoney(data.monto_pendiente || 0);
      const montoCuota = roundMoney(data.monto_cuota || (parseFloat(data.monto_financiado || 0) / parseInt(data.nivel_cashea, 10)));
      const sugerido = (pendiente < montoCuota || montoCuota <= 0) ? pendiente : montoCuota;
      document.getElementById('abonar_id').value = data.id_cashea_credito;
      document.getElementById('abonar_deuda_actual').textContent = formatMoney(pendiente);
      document.getElementById('abonar_cuota_sugerida').textContent = 'Cuota sugerida: ' + formatMoney(sugerido);
      const inputMonto = document.getElementById('abonar_monto');
      inputMonto.value = sugerido.toFixed(2);
      inputMonto.max = pendiente.toFixed(2);
      document.getElementById('modalAbonarCashea').classList.remove('hidden');
    }

    function closeAbonarModal() {
      document.getElementById('modalAbonarCashea').classList.add('hidden');
    }

    document.getElementById('edit_nivel').addEventListener('input', calcularEditCashea);
    document.getElementById('edit_porcentaje').addEventListener('change', calcularEditCashea);

    async function confirmDelete(id) {
      if (await showConfirm('¿Desea desactivar este crédito Cashea?', 'Desactivar crédito', 'Sí, desactivar')) {
        const f = new FormData();
        f.append('deleteCashea', 'true');
        f.append('id_cashea_credito', id);
        fetch('?url=cashea&type=main', { method: 'POST', body: f })
          .then(r => r.json()).then(d => {
            if (d.status === 'success') location.reload();
            else showAlert(d.message || 'No se pudo desactivar el crédito.', 'error', 'Error');
          });
      }
    }
  </script>
  <script src="assets/js/datatables-init.js"></script>
</body>
</html>
