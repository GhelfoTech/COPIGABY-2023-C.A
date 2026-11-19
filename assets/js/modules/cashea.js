(function (window, document, $) {
  'use strict';

  $(function () {
    window.CopiGabyShowModuleFlash();

    const modalCashea = document.getElementById('modalCashea');
    const formCashea = document.getElementById('formCashea');
    const selectPedido = document.getElementById('selectPedido');
    const nivelCashea = document.getElementById('nivelCashea');
    const porcentajeInicial = document.getElementById('porcentajeInicial');

    function roundMoney(value) {
      const num = parseFloat(value);
      if (isNaN(num)) return 0;
      return Math.round((num + Number.EPSILON) * 100) / 100;
    }

    function toggleModal(forceClose = false) {
      if (forceClose) {
    modalCashea.classList.add('hidden');
    modalCashea.setAttribute('aria-hidden', 'true');
    return;
      }

      formCashea.reset();
      calcularCashea();
      modalCashea.classList.remove('hidden');
      modalCashea.setAttribute('aria-hidden', 'false');
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

    function calcularCashea() {
      const option = selectPedido.options[selectPedido.selectedIndex];
      const total = roundMoney(option ? option.dataset.total : 0);
      const percent = parseInt(porcentajeInicial.value, 10);
      const nivel = parseInt(nivelCashea.value, 10);
      const porcentajeValido = [0, 10, 20, 30, 40, 50, 60].includes(percent);
      const montos = porcentajeValido
    ? calcularMontosCasheaJs(total, percent)
    : { inicial: 0, financiado: 0 };
      const financiado = total > 0 && porcentajeValido ? montos.financiado : 0;

      document.getElementById('lblMontoInicial').textContent = formatMoney(montos.inicial);
      document.getElementById('lblMontoFinanciado').textContent = formatMoney(financiado);
      document.getElementById('lblCuotas').textContent = textoCuotas(nivel, financiado);
    }

    function textoCuotas(nivel, financiado) {
      if (!nivel || nivel < 1 || financiado <= 0) {
    return 'Seleccione cuotas e inicial válidas';
      }
      const montoCuota = roundMoney(financiado / nivel);
      return nivel + ' cuotas estimadas de ' + formatMoney(montoCuota);
    }

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
    document.getElementById('btnOpenModal').addEventListener('click', () => toggleModal());
    selectPedido.addEventListener('change', calcularCashea);
    nivelCashea.addEventListener('change', calcularCashea);
    porcentajeInicial.addEventListener('change', calcularCashea);

    async function confirmDelete(id) {
      if (await showConfirm('¿Desea desactivar este crédito Cashea?', 'Desactivar crédito', 'Sí, desactivar')) {
    const f = new FormData();
    f.append('deleteCashea', 'true');
    f.append('id_cashea_credito', id);
    CopiGabyAjax.request('?url=cashea&type=main', { method: 'POST', body: f })
      .then(r => r.json())
      .then(d => showMutationResultAndReload(d, 'Crédito Cashea desactivado correctamente.', 'No se pudo desactivar el crédito.'))
      .catch(() => showAlert('No se pudo comunicar con el servidor para desactivar el crédito.', 'error', 'Error'));
      }
    }

    if (typeof roundMoney === "function") window.roundMoney = roundMoney;
    if (typeof toggleModal === "function") window.toggleModal = toggleModal;
    if (typeof toCents === "function") window.toCents = toCents;
    if (typeof fromCents === "function") window.fromCents = fromCents;
    if (typeof calcularCuotasPendientes === "function") window.calcularCuotasPendientes = calcularCuotasPendientes;
    if (typeof calcularMontosCasheaJs === "function") window.calcularMontosCasheaJs = calcularMontosCasheaJs;
    if (typeof formatMoney === "function") window.formatMoney = formatMoney;
    if (typeof calcularCashea === "function") window.calcularCashea = calcularCashea;
    if (typeof textoCuotas === "function") window.textoCuotas = textoCuotas;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof calcularEditCashea === "function") window.calcularEditCashea = calcularEditCashea;
    if (typeof openEditModal === "function") window.openEditModal = openEditModal;
    if (typeof closeEditModal === "function") window.closeEditModal = closeEditModal;
    if (typeof openAbonarModal === "function") window.openAbonarModal = openAbonarModal;
    if (typeof closeAbonarModal === "function") window.closeAbonarModal = closeAbonarModal;
    if (typeof confirmDelete === "function") window.confirmDelete = confirmDelete;
  });
})(window, document, window.jQuery);
