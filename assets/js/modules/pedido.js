(function (window, document, $) {
  'use strict';

  $(function () {
    const pedidoConfig = JSON.parse($('#pedidoConfig').attr('data-config'));
    const { productos, servicios, medidas, tasaActual, ivaDefaultCodigo, simboloMoneda } = pedidoConfig;
    const PORCENTAJES_INICIAL_CASHEA = [60, 50, 40, 30, 20, 10, 0];

    const modalPedido = document.getElementById('modalPedido');
    const modalDetalle = document.getElementById('modalDetalle');
    const btnOpen = document.getElementById('btnOpenModal');
    const closeBtns = document.querySelectorAll('.closeModalPedido');
    const overlayPedido = document.getElementById('overlayPedido');
    const itemsBody = document.getElementById('itemsBody');
    const totalGeneral = document.getElementById('totalGeneral');
    const subtotalGeneral = document.getElementById('subtotalGeneral');
    const montoIva = document.getElementById('montoIva');
    const totalBs = document.getElementById('totalBs');
    const formPedido = document.getElementById('formPedido');
    const btnSubmitPedido = document.getElementById('btnSubmitPedido');
    const itemsJsonInput = document.getElementById('itemsJson');
    const modalPedidoTitulo = document.getElementById('modalPedidoTitulo');
    const codigoPedidoEdit = document.getElementById('codigoPedidoEdit');
    const selectMetodo = document.getElementById('selectMetodo');
    const wrapMetodoPago = document.getElementById('wrapMetodoPago');
    const casheaConfig = document.getElementById('casheaConfig');
    document.body.appendChild(casheaConfig);
    casheaConfig.style.zIndex = '10000';
    const inputEsCashea = document.getElementById('inputEsCashea');
    const nivelCashea = document.getElementById('nivelCashea');
    const porcentajeInicialCashea = document.getElementById('porcentajeInicial');
    const buscarMetodoInput = document.getElementById('buscarMetodo');
    $('#referenciaPago').off('input.pedido').on('input.pedido', function () {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
    const wrapBanco = document.getElementById('wrapBanco');
    const wrapReferencia = document.getElementById('wrapReferencia');
    const selectIva = document.getElementById('selectIva');
    const inputSubtotal = document.getElementById('inputSubtotal');
    const inputMontoIva = document.getElementById('inputMontoIva');
    const inputPorcentajeIva = document.getElementById('inputPorcentajeIva');
    const inputMontoTotal = document.getElementById('inputMontoTotal');
    const inputTasaActual = document.getElementById('inputTasaActual');
    const selectCliente = document.getElementById('selectCliente');
    const modalClienteRegistro = document.getElementById('modalClienteRegistro');
    const formClienteRegistro = document.getElementById('formClienteRegistro');
    const btnNuevoClientePedido = document.getElementById('btnNuevoClientePedido');
    const btnGuardarClienteRegistro = document.getElementById('btnGuardarClienteRegistro');
    const buscarClienteInput = document.getElementById('buscarCliente');
    window.attachSearchableSelect(buscarClienteInput, selectCliente);
    window.attachSearchableSelect(buscarMetodoInput, selectMetodo);

    let itemCounter = 0;
    let editMode = false;
    let casheaMode = false;

    function abrirModalNuevoClientePedido() {
      formClienteRegistro.reset();
      modalClienteRegistro.classList.remove('hidden');
      modalClienteRegistro.setAttribute('aria-hidden', 'false');
      window.requestAnimationFrame(() => document.getElementById('clienteRegistroCedula').focus());
    }

    function cerrarModalNuevoClientePedido() {
      modalClienteRegistro.classList.add('hidden');
      modalClienteRegistro.setAttribute('aria-hidden', 'true');
    }

    btnNuevoClientePedido.addEventListener('click', abrirModalNuevoClientePedido);
    modalClienteRegistro.addEventListener('click', (event) => {
      if (event.target.closest('[data-close-cliente-modal]')) {
        cerrarModalNuevoClientePedido();
      }
    });

    formClienteRegistro.addEventListener('submit', async (event) => {
      event.preventDefault();

      const originalText = btnGuardarClienteRegistro.textContent;
      btnGuardarClienteRegistro.disabled = true;
      btnGuardarClienteRegistro.textContent = 'Guardando...';

      try {
        const response = await CopiGabyAjax.request(formClienteRegistro.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: new FormData(formClienteRegistro),
        });
        const result = await response.json();

        if (!response.ok || result.status !== 'success' || !result.cliente) {
          throw new Error(result.message || 'No se pudo registrar el cliente.');
        }

        const cliente = result.cliente;
        const cedulaCliente = String(cliente.cedula_cliente);
        const textoOpcion = `${cliente.nombre} (${cedulaCliente})`;
        let opcionCliente = [...selectCliente.options].find(option => option.value === cedulaCliente);

        if (opcionCliente) {
          opcionCliente.textContent = textoOpcion;
          opcionCliente.hidden = false;
          opcionCliente.selected = true;
        } else {
          opcionCliente = new Option(textoOpcion, cedulaCliente, true, true);
          selectCliente.add(opcionCliente);
        }

        selectCliente.value = cedulaCliente;
        window.syncSearchableSelect(selectCliente);

        cerrarModalNuevoClientePedido();
        formClienteRegistro.reset();

        await Swal.fire({
          icon: 'success',
          title: 'Cliente registrado',
          text: 'El cliente fue agregado y quedó seleccionado en el pedido.',
          timer: 1800,
          showConfirmButton: false,
        });
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'No se pudo registrar',
          text: error.message || 'Ocurrió un error al registrar el cliente.',
        });
      } finally {
        btnGuardarClienteRegistro.disabled = false;
        btnGuardarClienteRegistro.textContent = originalText;
      }
    });

    function roundMoney(value) {
      const num = parseFloat(value);
      if (isNaN(num)) return 0;
      return Math.round(num * 100) / 100;
    }

    function formatMoney(value) {
      return roundMoney(value).toFixed(2);
    }

    function getTasaFormulario() {
      const tasaInput = document.getElementById('tasaActualInput');
      const tasa = parseFloat(tasaInput ? tasaInput.value : tasaActual);
      return isNaN(tasa) ? parseFloat(tasaActual) : tasa;
    }

    function syncHiddenFinancialFields(subtotal, iva, porcentajeIva, total) {
      if (inputSubtotal) inputSubtotal.value = formatMoney(subtotal);
      if (inputMontoIva) inputMontoIva.value = formatMoney(iva);
      if (inputPorcentajeIva) inputPorcentajeIva.value = formatMoney(porcentajeIva);
      if (inputMontoTotal) inputMontoTotal.value = formatMoney(total);
      if (inputTasaActual) inputTasaActual.value = formatMoney(getTasaFormulario());
    }

    function getPorcentajeIvaSeleccionado() {
      if (!selectIva || selectIva.selectedIndex < 0) return 0;
      const option = selectIva.options[selectIva.selectedIndex];
      return parseFloat(option.getAttribute('data-porcentaje') || '0');
    }

    function computeFinancialTotals() {
      let subtotal = 0;
      itemsBody.querySelectorAll('tr[data-item]').forEach(row => {
        subtotal += parseFloat(row.dataset.subtotal || 0);
      });
      subtotal = roundMoney(subtotal);
      const porcentajeIva = getPorcentajeIvaSeleccionado();
      const iva = roundMoney(subtotal * (porcentajeIva / 100));
      const total = roundMoney(subtotal + iva);
      return { subtotal, porcentajeIva, iva, total };
    }

    function setIvaSeleccionado(codigoIva) {
      if (!selectIva) return;
      const codigo = String(codigoIva || ivaDefaultCodigo);
      if ([...selectIva.options].some(opt => opt.value === codigo)) {
        selectIva.value = codigo;
      } else if (selectIva.options.length > 0) {
        selectIva.selectedIndex = 0;
      }
    }

    function toggleModal(forceClose = false) {
      if (forceClose) {
        modalPedido.classList.add('hidden');
        return;
      }
      modalPedido.classList.toggle('hidden');
    }

    function resetFormPedido() {
      editMode = false;
      casheaMode = false;
      codigoPedidoEdit.value = '';
      formPedido.action = '?url=pedido&type=register';
      modalPedidoTitulo.textContent = 'Nuevo Pedido';
      btnSubmitPedido.textContent = 'Registrar Pedido';
      formPedido.reset();
      setCasheaMode(false);
      document.getElementById('tasaActualInput').value = formatMoney(tasaActual);
      if (inputTasaActual) inputTasaActual.value = formatMoney(tasaActual);
      setIvaSeleccionado(ivaDefaultCodigo);
      itemsBody.innerHTML = '<tr id="emptyRow"><td colspan="7" class="px-4 py-6 text-center text-gray-400 italic">Agregue al menos un producto o servicio</td></tr>';
      subtotalGeneral.textContent = simboloMoneda + '0.00';
      montoIva.textContent = simboloMoneda + '0.00';
      totalGeneral.textContent = 'Bs 0.00';
      totalBs.textContent = simboloMoneda + '0.00';
      syncHiddenFinancialFields(0, 0, getPorcentajeIvaSeleccionado(), 0);
      toggleCamposBanco();
      updateCasheaAmounts(0);
    }

    btnOpen.onclick = () => {
      resetFormPedido();
      toggleModal();
    };
    closeBtns.forEach(btn => btn.onclick = () => { toggleModal(true); resetFormPedido(); });
    overlayPedido.onclick = () => { toggleModal(true); resetFormPedido(); };

    function toggleCamposBanco() {
      const metodoSeleccionado = selectMetodo.selectedOptions[0];
      const nombreMetodo = metodoSeleccionado
        ? metodoSeleccionado.textContent.trim().toLocaleUpperCase('es').replace('Ó', 'O')
        : '';
      const requiere = !casheaMode && ['PAGO MOVIL', 'TRANSFERENCIA'].includes(nombreMetodo);
      wrapBanco.classList.toggle('hidden', !requiere);
      wrapReferencia.classList.toggle('hidden', !requiere);
      document.getElementById('selectBanco').required = requiere;
      document.getElementById('referenciaPago').required = requiere;
    }

    function setCasheaMode(enabled) {
      casheaMode = enabled && !editMode;
      inputEsCashea.value = casheaMode ? '1' : '0';
      nivelCashea.required = casheaMode;
      porcentajeInicialCashea.required = casheaMode;
      if (!casheaMode) {
        nivelCashea.value = '';
        porcentajeInicialCashea.value = '';
        casheaConfig.classList.add('hidden');
        casheaConfig.setAttribute('aria-hidden', 'true');
      }
      toggleCamposBanco();
      updateCasheaAmounts(computeFinancialTotals().total);
    }

    function updateCasheaPedidoResumen(total) {
      const cliente = selectCliente.selectedOptions[0];
      const nombreCliente = cliente && cliente.value ? cliente.textContent.trim() : 'Seleccione primero un cliente';
      document.getElementById('casheaPedidoResumen').textContent =
        `${nombreCliente} · Total: ${simboloMoneda}${formatMoney(total)}`;
    }

    function updateCasheaAmounts(total) {
      const percent = parseInt(porcentajeInicialCashea.value, 10);
      const validPercent = casheaMode && PORCENTAJES_INICIAL_CASHEA.includes(percent);
      const initial = validPercent ? roundMoney(total * percent / 100) : 0;
      const financed = casheaMode ? roundMoney(total - initial) : 0;
      const tasa = getTasaFormulario();
      document.getElementById('montoInicialCasheaBs').textContent = 'Bs ' + formatMoney(initial * tasa);
      document.getElementById('montoInicialCashea').textContent = simboloMoneda + formatMoney(initial);
      document.getElementById('montoFinanciadoCasheaBs').textContent = 'Bs ' + formatMoney(financed * tasa);
      document.getElementById('montoFinanciadoCashea').textContent = simboloMoneda + formatMoney(financed);
      updateCasheaPedidoResumen(total);
    }

    function esMetodoCashea() {
      const option = selectMetodo.selectedOptions[0];
      return Boolean(option && option.value && option.textContent.trim().toLocaleUpperCase().includes('CASHEA'));
    }

    function abrirModalCashea() {
      updateCasheaAmounts(computeFinancialTotals().total);
      casheaConfig.classList.remove('hidden');
      casheaConfig.setAttribute('aria-hidden', 'false');
    }

    function cancelarCashea() {
      selectMetodo.value = '';
      window.syncSearchableSelect(selectMetodo);
      setCasheaMode(false);
    }

    function cambiarMetodoPago() {
      if (esMetodoCashea()) {
        if (editMode) {
          selectMetodo.value = '';
          window.syncSearchableSelect(selectMetodo);
          Swal.fire({ icon: 'warning', title: 'No disponible', text: 'Cashea solo se puede seleccionar al registrar un pedido nuevo.' });
          return;
        }
        if (computeFinancialTotals().total <= 25) {
          selectMetodo.value = '';
          window.syncSearchableSelect(selectMetodo);
          Swal.fire({ icon: 'warning', title: 'Monto mínimo', text: 'Cashea solo está disponible para pedidos mayores a $25.' });
          return;
        }
        setCasheaMode(true);
        abrirModalCashea();
        return;
      }

      if (casheaMode) {
        setCasheaMode(false);
      } else {
        toggleCamposBanco();
      }
    }

    function buildOptions(tipo) {
      const list = tipo === 'producto' ? productos : servicios;
      let html = '<option value="">— Seleccionar —</option>';
      list.forEach(item => {
        const id = tipo === 'producto' ? item.codigo_producto : item.codigo_servicio;
        const nombre = tipo === 'producto' ? item.nombre_producto : item.nombre_servicio;
        const precio = tipo === 'producto' ? item.precio : item.precio;
        const stock = tipo === 'producto' ? item.stock_actual : '';
        const codigoMedia = tipo === 'producto' ? (item.codigo_media || '') : '';
        const extra = stock !== '' ? ` data-stock="${stock}"` : '';
        const extraUnidad = tipo === 'producto' ? ` data-codigo-media="${codigoMedia}"` : '';
        html += `<option value="${id}" data-precio="${parseFloat(precio)}"${extra}${extraUnidad}>${nombre}</option>`;
      });
      return html;
    }

    function recalcTotal() {
      const { subtotal, porcentajeIva, iva, total } = computeFinancialTotals();
      const tasa = getTasaFormulario();

      if (casheaMode && total <= 25) {
        selectMetodo.value = '';
        window.syncSearchableSelect(selectMetodo);
        setCasheaMode(false);
        Swal.fire({ icon: 'warning', title: 'Monto mínimo', text: 'Cashea solo está disponible para pedidos mayores a $25.' });
      }

      subtotalGeneral.textContent = simboloMoneda + formatMoney(subtotal);
      montoIva.textContent = simboloMoneda + formatMoney(iva);
      totalGeneral.textContent = 'Bs ' + formatMoney(total * tasa);
      totalBs.textContent = simboloMoneda + formatMoney(total);
      syncHiddenFinancialFields(subtotal, iva, porcentajeIva, total);
      updateCasheaAmounts(total);
    }

    function getCantidadTotal(row) {
      return Number(row.querySelector('.item-cant').value);
    }

    function updateRowSubtotal(row) {
      const inputCantidad = row.querySelector('.item-cant');
      const cantidadTotal = getCantidadTotal(row);
      if (!Number.isFinite(cantidadTotal) || cantidadTotal <= 0) {
        inputCantidad.setCustomValidity('La cantidad debe ser un número mayor a cero (se permiten decimales).');
        return;
      }
      inputCantidad.setCustomValidity('');
      const precio = parseFloat(row.querySelector('.item-precio').value) || 0;
      const subtotal = roundMoney(cantidadTotal * precio);
      row.querySelector('.item-subtotal').textContent = simboloMoneda + formatMoney(subtotal);
      row.dataset.subtotal = String(subtotal);
      recalcTotal();
    }

    function onTipoChange(row) {
      const tipo = row.querySelector('.item-tipo').value;
      row.querySelector('.item-select').innerHTML = buildOptions(tipo);
      const unidadSelect = row.querySelector('.item-unidad');
      if (unidadSelect) {
        unidadSelect.value = '';
        unidadSelect.disabled = tipo === 'servicio';
        unidadSelect.classList.toggle('bg-gray-100', unidadSelect.disabled);
        unidadSelect.classList.toggle('cursor-not-allowed', unidadSelect.disabled);
      }
      row.querySelector('.item-precio').value = '';
      row.querySelector('.item-cant').value = '1';
      row.querySelector('.item-search').value = '';
      window.refreshSearchableSelect(row.querySelector('.item-select'));
      row.querySelector('.item-subtotal').textContent = simboloMoneda + '0.00';
      row.querySelector('.item-unidad-info').textContent = '';
      row.querySelector('.item-stock-info').textContent = '';
      row.dataset.subtotal = '0';
      recalcTotal();
    }

    function onItemSelect(row) {
      const selectItem = row.querySelector('.item-select');
      const option = selectItem ? selectItem.options[selectItem.selectedIndex] : null;
      const precio = option ? option.getAttribute('data-precio') : '';
      const precioInput = row.querySelector('.item-precio');
      if (precioInput) precioInput.value = precio !== null && precio !== '' ? precio : '';

      const unidadSel = row.querySelector('.item-unidad');
      const stockInfo = row.querySelector('.item-stock-info');

      if (unidadSel) {
        if (option && option.value) {
          const codigoMedia = option.getAttribute('data-codigo-media');
          if (codigoMedia) {
            unidadSel.value = codigoMedia;
          } else if (row.querySelector('.item-tipo').value === 'producto') {
            unidadSel.value = '';
          }
        } else {
          unidadSel.value = '';
        }
      }

      // Sin selección no debe quedar visible el stock del producto anterior.
      if (stockInfo) {
        const stock = option && option.value ? option.getAttribute('data-stock') : null;
        stockInfo.textContent = stock !== null ? `Stock disponible: ${stock}` : '';
      }

      if (!option || !option.value) {
        row.querySelector('.item-precio').value = '';
        row.querySelector('.item-unidad-info').textContent = '';
        row.dataset.subtotal = '0';
        row.querySelector('.item-subtotal').textContent = simboloMoneda + '0.00';
        recalcTotal();
        return;
      }

      updateRowSubtotal(row);
    }

    async function removeItemRow(tr) {
      const confirmado = await showConfirm(
        '¿Desea quitar este producto o servicio del pedido?',
        'Quitar elemento',
        'Sí, quitar'
      );
      if (!confirmado || !tr || !tr.parentNode) return;

      tr.remove();
      if (!itemsBody.querySelector('tr[data-item]')) {
        itemsBody.innerHTML = '<tr id="emptyRow"><td colspan="7" class="px-4 py-6 text-center text-gray-400 italic">Agregue al menos un producto o servicio</td></tr>';
        subtotalGeneral.textContent = simboloMoneda + '0.00';
        montoIva.textContent = simboloMoneda + '0.00';
        totalGeneral.textContent = 'Bs 0.00';
        totalBs.textContent = simboloMoneda + '0.00';
      } else {
        recalcTotal();
      }
    }

    function bindItemRow(tr) {
      tr.querySelector('.item-tipo').addEventListener('change', () => onTipoChange(tr));
      tr.querySelector('.item-select').addEventListener('change', () => onItemSelect(tr));
      tr.querySelector('.item-cant').addEventListener('input', () => updateRowSubtotal(tr));
      tr.querySelector('.item-cant').addEventListener('change', () => updateRowSubtotal(tr));
      tr.querySelector('.item-unidad').addEventListener('change', () => updateRowSubtotal(tr));
      tr.querySelector('.btn-remove').addEventListener('click', () => removeItemRow(tr));
    }

    function populateUnidadSelect(row) {
      const unidadSel = row.querySelector('.item-unidad');
      if (!unidadSel) return;
      // Selección simple con todas las unidades del módulo, permitiendo dejarla vacía
      let html = '<option value="">— Sin unidad —</option>';
      medidas.forEach(m => {
        html += `<option value="${m.codigo_media}">${m.nombre}${m.abreviatura ? ' (' + m.abreviatura + ')' : ''}</option>`;
      });
      unidadSel.innerHTML = html;
    }

    function addItemRow(prefill = null) {
      const emptyRow = document.getElementById('emptyRow');
      if (emptyRow) emptyRow.remove();

      itemCounter += 1;
      const tr = document.createElement('tr');
      tr.id = 'item-' + itemCounter;
      tr.dataset.item = '1';
      tr.dataset.subtotal = '0';
      tr.innerHTML = `
        <td class="px-2 py-2">
          <select class="item-tipo w-full px-2 py-1.5 bg-gray-50 border rounded-lg text-xs font-bold focus:border-orange outline-none">
            <option value="producto">Producto</option>
            <option value="servicio">Servicio</option>
          </select>
        </td>
         <td class="px-2 py-2">
           <div class="relative mb-1">
             <input type="search" class="item-search w-full px-2 py-1.5 bg-gray-50 border rounded-lg text-xs focus:border-orange outline-none" placeholder="Escriba producto o servicio" aria-label="Buscar producto o servicio">
           </div>
           <select class="item-select hidden" required></select>
           <div class="item-unidad-info text-[0.65rem] text-gray-500 font-semibold mt-1"></div>
           <div class="item-stock-info text-[0.65rem] text-orange font-semibold mt-0.5"></div>
         </td>
        <td class="px-2 py-2 item-unidad-cell">
          <select class="item-unidad w-full px-2 py-1.5 bg-gray-50 border rounded-lg text-xs font-bold focus:border-orange outline-none">
            <option value="">— Sin unidad —</option>
          </select>
        </td>
        <td class="px-2 py-2">
          <input type="number" step="0.01" min="0.01" value="1" class="item-cant w-full px-2 py-1.5 bg-white border rounded-lg text-xs font-bold focus:border-orange outline-none">
        </td>
        <td class="px-2 py-2">
          <input type="number" step="0.01" min="0" readonly tabindex="-1" class="item-precio w-full px-2 py-1.5 bg-slate-800 text-slate-400 cursor-not-allowed border rounded-lg text-xs font-bold outline-none">
        </td>
        <td class="px-2 py-2 text-right font-black text-navy-dark item-subtotal">${simboloMoneda}0.00</td>
        <td class="px-2 py-2 text-center">
          <button type="button" class="btn-remove text-red-400 hover:text-red-600 p-1" title="Quitar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </td>
      `;

      itemsBody.appendChild(tr);
      bindItemRow(tr);
      window.attachSearchableSelect(tr.querySelector('.item-search'), tr.querySelector('.item-select'));
      populateUnidadSelect(tr);

      if (prefill) {
        tr.querySelector('.item-tipo').value = prefill.tipo;
        tr.querySelector('.item-select').innerHTML = buildOptions(prefill.tipo);
         const selectVal = prefill.tipo === 'producto' ? prefill.codigo_producto : prefill.codigo_servicio;
         tr.querySelector('.item-select').value = String(selectVal);
        window.syncSearchableSelect(tr.querySelector('.item-select'));
         const unidadSel = tr.querySelector('.item-unidad');
         if (unidadSel && prefill.codigo_unidad_medida) unidadSel.value = String(prefill.codigo_unidad_medida);
        if (unidadSel) {
          unidadSel.disabled = prefill.tipo === 'servicio';
          unidadSel.classList.toggle('bg-gray-100', unidadSel.disabled);
          unidadSel.classList.toggle('cursor-not-allowed', unidadSel.disabled);
        }

         tr.querySelector('.item-cant').value = String(parseFloat(prefill.cantidad) || 1);

        tr.querySelector('.item-precio').value = formatMoney(prefill.precio_venta);
        updateRowSubtotal(tr);
     } else {
         tr.querySelector('.item-select').innerHTML = buildOptions('producto');
     }
    }

    function collectItemsFromRows() {
      const rows = itemsBody.querySelectorAll('tr[data-item]');
      if (rows.length === 0) {
        return { ok: false, message: 'Debe agregar al menos un ítem al pedido.', items: [] };
      }

      const items = [];
      for (const row of rows) {
        const tipo = row.querySelector('.item-tipo').value;
        const selectItem = row.querySelector('.item-select');
        const id = selectItem.value;
        const cantidadTotal = getCantidadTotal(row);
        const precio = parseFloat(row.querySelector('.item-precio').value);

        if (!id) return { ok: false, message: 'Seleccione producto o servicio en cada línea.', items: [] };
        if (!Number.isFinite(cantidadTotal) || cantidadTotal <= 0) {
          return { ok: false, message: 'Cada cantidad debe ser un número mayor a cero (se permiten decimales).', items: [] };
        }
        if (isNaN(precio) || precio < 0) return { ok: false, message: 'Precio no válido en una de las líneas.', items: [] };

        const item = { tipo, cantidad: cantidadTotal, precio_venta: roundMoney(precio) };
        if (tipo === 'producto') {
          item.codigo_producto = parseInt(id, 10);
          const option = selectItem.options[selectItem.selectedIndex];
          const stockAttr = option.getAttribute('data-stock');
          const stock = stockAttr !== null ? parseInt(stockAttr, 10) : null;
          if (stock !== null && cantidadTotal > stock) {
            return { ok: false, message: 'Stock insuficiente para: ' + option.text, items: [] };
          }
          const unidadSelect = row.querySelector('.item-unidad');
          if (unidadSelect) {
            item.codigo_unidad_medida = parseInt(unidadSelect.value, 10) || null;
          }
        } else {
          item.codigo_servicio = parseInt(id, 10);
        }
        items.push(item);
      }
      return { ok: true, message: '', items };
    }

    async function submitPedido(e) {
      e.preventDefault();

      // En edición el cliente ya pertenece al pedido; solo se exige al registrar
      if (!selectCliente.value && !editMode) {
        Swal.fire({ icon: 'warning', title: 'Cliente requerido', text: 'Seleccione un cliente.' });
        return;
      }

      if (casheaMode) {
        const nivel = parseInt(nivelCashea.value, 10);
        const porcentaje = parseInt(porcentajeInicialCashea.value, 10);
        if (
          !esMetodoCashea()
          || !Number.isInteger(nivel)
          || nivel < 1
          || nivel > 6
          || !PORCENTAJES_INICIAL_CASHEA.includes(porcentaje)
          || !casheaConfig.classList.contains('hidden')
        ) {
          Swal.fire({ icon: 'warning', title: 'Configuración incompleta', text: 'Confirme una configuración válida de Cashea antes de guardar el pedido.' });
          return;
        }
      }

      const collected = collectItemsFromRows();
      if (!collected.ok) {
        Swal.fire({ icon: 'warning', title: 'Detalle incompleto', text: collected.message });
        return;
      }

      recalcTotal();
      const totals = computeFinancialTotals();
      if (casheaMode && totals.total <= 25) {
        Swal.fire({ icon: 'warning', title: 'Monto mínimo', text: 'Cashea solo está disponible para pedidos mayores a $25.' });
        return;
      }

      const formData = new FormData(formPedido);
      const esCashea = casheaMode && !editMode;
      formData.set('items', JSON.stringify(collected.items));
      formData.set('codigo_IVA', selectIva ? selectIva.value : String(ivaDefaultCodigo));
      formData.set('subtotal', formatMoney(totals.subtotal));
      formData.set('monto_iva', formatMoney(totals.iva));
      formData.set('porcentaje_iva', formatMoney(totals.porcentajeIva));
      formData.set('monto_total', formatMoney(totals.total));
      formData.set('tasa_actual', formatMoney(getTasaFormulario()));
      formData.set('es_cashea', esCashea ? '1' : '0');
      // En edición, si no se tocó el método de pago no se envía: el backend conserva el pago existente
      if (!editMode || selectMetodo.value) {
        formData.set('codigo_metodo', esCashea && porcentajeInicialCashea.value === '0' ? '0' : selectMetodo.value);
      }
      if (esCashea) {
        formData.set('nivel_cashea', nivelCashea.value);
        formData.set('porcentaje_inicial', porcentajeInicialCashea.value);
      }
      if (!wrapBanco.classList.contains('hidden')) {
        formData.set('codigo_banco', document.getElementById('selectBanco').value);
        formData.set('referencia_pago', document.getElementById('referenciaPago').value.trim());
      }
      itemsJsonInput.value = JSON.stringify(collected.items);

      btnSubmitPedido.disabled = true;
      btnSubmitPedido.textContent = 'Guardando...';

      const url = editMode ? '?url=pedido&type=update' : '?url=pedido&type=register';

      try {
        const response = await CopiGabyAjax.request(url, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: formData,
        });
        const data = await response.json();

        if (data.status === 'success') {
          modalPedido.classList.add('hidden');
          resetFormPedido();

          await Swal.fire({ icon: 'success', title: 'Éxito', text: data.message || 'Operación completada.', timer: 1500, showConfirmButton: false });
          if (!reloadPedidosTable()) {
            try {
              await CopiGabyAjax.refresh();
            } catch (refreshError) {
              console.error('El pedido se guardó, pero no se pudo actualizar el listado.', refreshError);
            }
          }
          return;
        }
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo procesar el pedido.' });
      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error de comunicación', text: 'Revise la consola o el log de PHP.' });
      } finally {
        btnSubmitPedido.disabled = false;
        btnSubmitPedido.textContent = editMode ? 'Actualizar Pedido' : 'Registrar Pedido';
      }
    }

    const $tablaPedidos = $('#tablaPedidos');

    function reloadPedidosTable() {
      if ($.fn.DataTable && $.fn.DataTable.isDataTable($tablaPedidos)) {
        $tablaPedidos.DataTable().ajax.reload(null, false);
        return true;
      }
      return false;
    }

    let detailPedidoId = null;

    function viewDetails(id) {
      CopiGabyAjax.request('?url=pedido&type=details', {
        method: 'POST',
        data: { id: id }
      })
        .then(r => r.json())
        .then(data => {
          const h = data.header;
          const items = data.items;
          if (!h) return;

          detailPedidoId = h.codigo_pedido;

          document.getElementById('det_codigo').innerText = 'PEDIDO #' + String(h.codigo_pedido).padStart(4, '0');
          const f = new Date(h.fecha_pedido.replace(' ', 'T'));
          document.getElementById('det_fecha').innerText = 'Fecha: ' + f.toLocaleDateString('es-VE') + ' ' + f.toLocaleTimeString('es-VE', {hour: '2-digit', minute: '2-digit'});
          document.getElementById('det_cliente').innerText = h.nombre_cliente || '—';
          document.getElementById('det_telefono').innerText = 'Tel: ' + (h.telefono_cliente || '—');
          document.getElementById('det_usuario').innerText = h.nombre_usuario || '—';

          const subtotalDet = parseFloat(h.subtotal ?? 0);
          const totalDet = parseFloat(h.monto_total ?? 0);
          const ivaDet = parseFloat(h.monto_iva ?? 0);
          const pctIvaDet = parseFloat(h.porcentaje_iva ?? 0);

          document.getElementById('det_subtotal').innerText = simboloMoneda + formatMoney(subtotalDet);
          document.getElementById('det_total').innerText = simboloMoneda + formatMoney(totalDet);
          const tasaDet = parseFloat(h.tasa_actual ?? h.tasa_cambio ?? tasaActual) || parseFloat(tasaActual);
          document.getElementById('det_total_bs').innerText = formatMoney(totalDet * tasaDet) + ' Bs';
          document.getElementById('det_iva').innerText = simboloMoneda + formatMoney(ivaDet) + ' (' + formatMoney(pctIvaDet) + '%)';
          const pago = h.pago || null;

          const bancoWrap = document.getElementById('det_banco_wrap');
          const refWrap = document.getElementById('det_ref_wrap');
          if (pago && pago.nombre_banco) {
            bancoWrap.classList.remove('hidden');
            document.getElementById('det_banco').innerText = `${String(pago.codigo_banco ?? '').padStart(4, '0')} - ${pago.nombre_banco}`;
          } else {
            bancoWrap.classList.add('hidden');
          }
          const refPago = pago ? (pago.numero_comprobante || pago.referencia || '') : '';
          if (refPago) {
            refWrap.classList.remove('hidden');
            document.getElementById('det_referencia').innerText = refPago;
          } else {
            refWrap.classList.add('hidden');
          }

          const tbody = document.querySelector('#tablaDetallesItems tbody');
          tbody.innerHTML = '';
           items.forEach(it => {
             const nombre = it.tipo === 'servicio' ? it.nombre_servicio : it.nombre_producto;
             const unidadDisplay = it.tipo === 'producto' ? (it.abreviatura_medida || it.nombre_medida || '—') : '—';
             tbody.innerHTML += `
               <tr>
                 <td class="px-4 py-3 uppercase">${nombre}</td>
                 <td class="px-4 py-3 text-center text-xs uppercase text-gray-500">${it.tipo}</td>
                 <td class="px-4 py-3 text-center font-bold text-navy-light">${unidadDisplay}</td>
                 <td class="px-4 py-3 text-center">${parseFloat(it.cantidad)}</td>
                 <td class="px-4 py-3 text-right">${simboloMoneda}${formatMoney(parseFloat(it.precio_venta))}</td>
                 <td class="px-4 py-3 text-right font-black text-orange-dk">${simboloMoneda}${formatMoney(parseFloat(it.subtotal))}</td>
               </tr>`;
           });

          const esCashea = parseInt(h.id_cashea_credito, 10) > 0;
          const activo = parseInt(h.estado, 10) === 1;
          document.getElementById('det_metodo').innerText = h.nombre_metodo || (esCashea ? 'Cashea (sin inicial)' : '—');
          document.getElementById('det_monto_pago').innerText = pago
            ? simboloMoneda + formatMoney(parseFloat(pago.monto))
            : simboloMoneda + formatMoney(esCashea ? 0 : totalDet);
          document.getElementById('det_cashea_wrap').classList.toggle('hidden', !esCashea);
          if (esCashea) {
            document.getElementById('det_cashea_nivel').innerText = h.nivel_cashea ? 'Nivel ' + h.nivel_cashea : 'No especificado';
            document.getElementById('det_cashea_inicial').innerText = simboloMoneda + formatMoney(parseFloat(h.monto_inicial_pagado || 0));
            document.getElementById('det_cashea_saldo').innerText = simboloMoneda + formatMoney(parseFloat(h.monto_pendiente || h.monto_financiado || 0));
          }
          document.getElementById('btnDetalleEditar').classList.toggle('hidden', !activo || esCashea);
          document.getElementById('btnDetalleEliminar').classList.toggle('hidden', !activo);
          document.getElementById('btnDetalleEditar').onclick = () => {
            closeDetalleModal();
            openEditPedido(detailPedidoId);
          };
          document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(detailPedidoId);

          modalDetalle.classList.remove('hidden');
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar el detalle del pedido.' }));
    }

    function closeDetalleModal() {
      modalDetalle.classList.add('hidden');
    }

    function openEditPedido(id) {
      CopiGabyAjax.request('?url=pedido&type=details', {
        method: 'POST',
        data: { id: id }
      })
        .then(r => r.json())
        .then(async data => {
          const h = data.header;
          const items = data.items;
          if (!h) return;

          resetFormPedido();
          // searchable-select completes its native form reset on the next task.
          await new Promise(resolve => window.setTimeout(resolve, 0));
          editMode = true;
          codigoPedidoEdit.value = h.codigo_pedido;
          formPedido.action = '?url=pedido&type=update';
          modalPedidoTitulo.textContent = 'Modificar Pedido #' + String(h.codigo_pedido).padStart(4, '0');
          btnSubmitPedido.textContent = 'Actualizar Pedido';

          document.getElementById('selectCliente').value = String(h.cedula_cliente);
          window.syncSearchableSelect(selectCliente);
          // Mantener la fecha original del pedido al modificar
          const fechaPedidoEdit = document.querySelector('#formPedido [name="fecha_pedido"]');
          if (fechaPedidoEdit) fechaPedidoEdit.value = String(h.fecha_pedido).slice(0, 10);
          // En edición no se fuerza volver a elegir cliente/método: ya vienen del pedido
          selectMetodo.required = false;
          setIvaSeleccionado(h.codigo_IVA || ivaDefaultCodigo);

          const tasaPed = parseFloat(h.tasa_actual ?? h.tasa_cambio ?? tasaActual);
          document.getElementById('tasaActualInput').value = formatMoney(tasaPed);
          if (inputTasaActual) inputTasaActual.value = formatMoney(tasaPed);
          document.getElementById('tasaActualLabel').textContent = formatMoney(tasaPed);

           items.forEach(it => {
             addItemRow({
               tipo: it.tipo,
               codigo_producto: it.codigo_producto,
               codigo_servicio: it.codigo_servicio,
               cantidad: it.cantidad,
               precio_venta: it.precio_venta,
               codigo_media: it.codigo_media,
             });
           });

          if (h.codigo_metodo) {
            selectMetodo.value = String(h.codigo_metodo);
            window.syncSearchableSelect(selectMetodo);
          }
          toggleCamposBanco();

          const pago = h.pago || null;
          if (pago && pago.codigo_banco) {
            document.getElementById('selectBanco').value = String(pago.codigo_banco);
          }
          const refEdit = pago ? (pago.numero_comprobante || pago.referencia || '') : '';
          if (refEdit) {
            document.getElementById('referenciaPago').value = refEdit;
          }

          const subtotalH = parseFloat(h.subtotal ?? 0);
          const ivaH = parseFloat(h.monto_iva ?? 0);
          const pctH = parseFloat(h.porcentaje_iva ?? getPorcentajeIvaSeleccionado());
          const totalH = parseFloat(h.monto_total ?? 0);

          subtotalGeneral.textContent = simboloMoneda + formatMoney(subtotalH);
          montoIva.textContent = simboloMoneda + formatMoney(ivaH);
          totalGeneral.textContent = 'Bs ' + formatMoney(totalH * tasaPed);
          totalBs.textContent = simboloMoneda + formatMoney(totalH);
          syncHiddenFinancialFields(subtotalH, ivaH, pctH, totalH);

          modalPedido.classList.remove('hidden');
        })
        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar el pedido para edición.' }));
    }

    function confirmDelete(id) {
      Swal.fire({
        title: '¿Anular pedido?',
        text: 'Se revertirá el inventario y el pedido quedará inactivo. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar',
      }).then(result => {
        if (!result.isConfirmed) return;
        const f = new FormData();
        f.append('deletePedido', 'true');
        f.append('idpedido', String(id));
        CopiGabyAjax.request('?url=pedido&type=main', { method: 'POST', body: f })
          .then(r => r.json())
          .then(d => {
            if (d.status === 'success') {
              Swal.fire({ icon: 'success', title: 'Anulado', text: d.message || 'Pedido anulado.', timer: 1500, showConfirmButton: false })
                .then(() => {
                  if (!reloadPedidosTable()) {
                    CopiGabyAjax.refresh().catch(refreshError => {
                      console.error('El pedido se anuló, pero no se pudo actualizar el listado.', refreshError);
                    });
                  }
                });
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'No se pudo anular el pedido.' });
            }
          })
          .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación al anular el pedido.' }));
      });
    }

    selectMetodo.addEventListener('change', cambiarMetodoPago);
    selectCliente.addEventListener('change', () => updateCasheaPedidoResumen(computeFinancialTotals().total));
    casheaConfig.querySelectorAll('[data-cashea-cancel]').forEach(button => {
      button.addEventListener('click', cancelarCashea);
    });
    document.getElementById('btnConfirmarCashea').addEventListener('click', () => {
      const nivel = parseInt(nivelCashea.value, 10);
      const porcentaje = parseInt(porcentajeInicialCashea.value, 10);
      if (!selectCliente.value) {
        Swal.fire({ icon: 'warning', title: 'Cliente requerido', text: 'Seleccione un cliente antes de configurar Cashea.' });
        return;
      }
      if (nivel < 1 || nivel > 6 || !PORCENTAJES_INICIAL_CASHEA.includes(porcentaje)) {
        Swal.fire({ icon: 'warning', title: 'Configuración incompleta', text: 'Seleccione la cantidad de cuotas y el porcentaje inicial.' });
        return;
      }
      casheaConfig.classList.add('hidden');
      casheaConfig.setAttribute('aria-hidden', 'true');
    });
    nivelCashea.addEventListener('change', () => {
      const inicialSugeridaPorNivel = {
        '1': '60',
        '2': '50',
        '3': '40',
        '4': '30',
        '5': '20',
        '6': '0',
      };
      porcentajeInicialCashea.value = inicialSugeridaPorNivel[nivelCashea.value] || '';
      updateCasheaAmounts(computeFinancialTotals().total);
    });
    porcentajeInicialCashea.addEventListener('change', () => {
      updateCasheaAmounts(computeFinancialTotals().total);
    });
    if (selectIva) {
      selectIva.addEventListener('change', recalcTotal);
    }

    document.getElementById('btnAddItem').addEventListener('click', () => addItemRow());
    formPedido.addEventListener('submit', submitPedido);

    function escapePedidoTableText(value) {
      return String(value ?? '').replace(/[&<>"']/g, function (character) {
        return {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#39;'
        }[character];
      });
    }

    function formatPedidoDate(value) {
      if (!value) return '—';
      const parsed = new Date(String(value).replace(' ', 'T'));
      if (Number.isNaN(parsed.getTime())) return String(value);

      const fecha = parsed.toLocaleDateString('es-VE');
      const hora = parsed.toLocaleTimeString('es-VE', {
        hour: '2-digit',
        minute: '2-digit',
        hourCycle: 'h23'
      });
      return fecha + ' ' + hora;
    }

    // Configuración DataTables Server-Side.
    window.CopiGabyDataTablesConfig = window.CopiGabyDataTablesConfig || {};
    window.CopiGabyDataTablesConfig.pedidos = {
      serverSide: true,
      processing: true,
      searchDelay: 350,
      ajax: {
        url: '?url=pedido&type=list',
        type: 'POST',
        dataType: 'json'
      },
      pageLength: 10,
      lengthMenu: [[10, 25, 50], [10, 25, 50]],
      columns: [
        {
          data: 'codigo_pedido',
          className: 'px-6 py-4 text-center text-gray-400 font-bold',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '<span class="font-bold text-gray-500">#'
              + escapePedidoTableText(String(data).padStart(4, '0'))
              + '</span>';
          }
        },
        {
          data: 'nombre_cliente',
          className: 'px-6 py-4',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '<span class="uppercase text-[0.8rem] font-bold text-navy-light">'
              + escapePedidoTableText(data || '—')
              + '</span>';
          }
        },
        {
          data: 'nombre_usuario',
          className: 'px-6 py-4 text-gray-500',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '<span class="text-gray-500">'
              + escapePedidoTableText(data || '—')
              + '</span>';
          }
        },
        {
          data: 'fecha_pedido',
          className: 'px-6 py-4 text-gray-500',
          render: function (data, type) {
            return type === 'display'
              ? escapePedidoTableText(formatPedidoDate(data))
              : data;
          }
        },
        {
          data: 'monto_total',
          className: 'px-6 py-4 text-right',
          render: function (data, type, row) {
            if (type !== 'display') return data;

            const monto = Number(data) || 0;
            const tasa = Number(row.tasa_cambio ?? tasaActual) || Number(tasaActual) || 0;
            const totalMoneda = monto.toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            });
            const totalBs = (monto * tasa).toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            });

            return '<div class="font-black text-navy-dark text-[1rem]">'
              + escapePedidoTableText(simboloMoneda) + totalMoneda
              + '</div><div class="text-[0.65rem] text-orange-dk font-bold">'
              + totalBs + ' Bs</div>';
          }
        },
        {
          data: 'nombre_metodo',
          className: 'px-6 py-4',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '<span class="text-xs font-bold text-gray-500 uppercase">'
              + escapePedidoTableText(data || '—')
              + '</span>';
          }
        },
        {
          data: 'estado',
          className: 'px-6 py-4 text-center',
          render: function (data, type) {
            if (type !== 'display') return data;
            const activo = Number(data) === 1;
            return '<span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase '
              + (activo ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600')
              + '">' + (activo ? 'Activo' : 'Anulado') + '</span>';
          }
        },
        {
          data: 'codigo_pedido',
          className: 'px-6 py-4 text-center',
          orderable: false,
          searchable: false,
          render: function () {
            return '<button type="button" class="js-view-order-details group relative text-orange-dk p-2 hover:bg-orange/10 rounded-lg transition-colors" title="Ver detalle">'
              + '<span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Consultar</span>'
              + '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'
              + '</button>';
          }
        }
      ],
      createdRow: function (row) {
        row.classList.add('hover:bg-gray-50/80', 'transition-colors');
      },
      initComplete: function () {
        const api = this.api();
        $(api.table().body()).on('click', '.js-view-order-details', function () {
          const pedido = api.row($(this).closest('tr')).data();
          if (pedido) viewDetails(pedido.codigo_pedido);
        });
      }
    };

    if (typeof abrirModalNuevoClientePedido === "function") window.abrirModalNuevoClientePedido = abrirModalNuevoClientePedido;
    if (typeof cerrarModalNuevoClientePedido === "function") window.cerrarModalNuevoClientePedido = cerrarModalNuevoClientePedido;
    if (typeof roundMoney === "function") window.roundMoney = roundMoney;
    if (typeof formatMoney === "function") window.formatMoney = formatMoney;
    if (typeof getTasaFormulario === "function") window.getTasaFormulario = getTasaFormulario;
    if (typeof syncHiddenFinancialFields === "function") window.syncHiddenFinancialFields = syncHiddenFinancialFields;
    if (typeof getPorcentajeIvaSeleccionado === "function") window.getPorcentajeIvaSeleccionado = getPorcentajeIvaSeleccionado;
    if (typeof computeFinancialTotals === "function") window.computeFinancialTotals = computeFinancialTotals;
    if (typeof setIvaSeleccionado === "function") window.setIvaSeleccionado = setIvaSeleccionado;
    if (typeof toggleModal === "function") window.toggleModal = toggleModal;
    if (typeof resetFormPedido === "function") window.resetFormPedido = resetFormPedido;
    if (typeof toggleCamposBanco === "function") window.toggleCamposBanco = toggleCamposBanco;
    if (typeof setCasheaMode === "function") window.setCasheaMode = setCasheaMode;
    if (typeof updateCasheaPedidoResumen === "function") window.updateCasheaPedidoResumen = updateCasheaPedidoResumen;
    if (typeof updateCasheaAmounts === "function") window.updateCasheaAmounts = updateCasheaAmounts;
    if (typeof esMetodoCashea === "function") window.esMetodoCashea = esMetodoCashea;
    if (typeof abrirModalCashea === "function") window.abrirModalCashea = abrirModalCashea;
    if (typeof cancelarCashea === "function") window.cancelarCashea = cancelarCashea;
    if (typeof cambiarMetodoPago === "function") window.cambiarMetodoPago = cambiarMetodoPago;
    if (typeof buildOptions === "function") window.buildOptions = buildOptions;
    if (typeof recalcTotal === "function") window.recalcTotal = recalcTotal;
    if (typeof getCantidadTotal === "function") window.getCantidadTotal = getCantidadTotal;
    if (typeof updateRowSubtotal === "function") window.updateRowSubtotal = updateRowSubtotal;
    if (typeof onTipoChange === "function") window.onTipoChange = onTipoChange;
    if (typeof onItemSelect === "function") window.onItemSelect = onItemSelect;
    if (typeof removeItemRow === "function") window.removeItemRow = removeItemRow;
    if (typeof bindItemRow === "function") window.bindItemRow = bindItemRow;
    if (typeof populateUnidadSelect === "function") window.populateUnidadSelect = populateUnidadSelect;
    if (typeof addItemRow === "function") window.addItemRow = addItemRow;
    if (typeof collectItemsFromRows === "function") window.collectItemsFromRows = collectItemsFromRows;
    if (typeof submitPedido === "function") window.submitPedido = submitPedido;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof openEditPedido === "function") window.openEditPedido = openEditPedido;
    if (typeof confirmDelete === "function") window.confirmDelete = confirmDelete;
  });
})(window, document, window.jQuery);
