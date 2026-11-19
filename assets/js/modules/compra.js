(function (window, document, $) {
  'use strict';

  $(function () {
    function openProductoModal() {
      resetSelectFilter(document.getElementById('selectCategoriaCompra'));
      document.getElementById('modalProductoCompra').classList.remove('hidden');
    }

    function closeProductoModal() {
      document.getElementById('modalProductoCompra').classList.add('hidden');
    }

    document.getElementById('formProductoCompra').addEventListener('submit', async function(event) {
      event.preventDefault();
      const formulario = event.currentTarget;
      try {
    const respuesta = await CopiGabyAjax.request(formulario.action, { method: 'POST', body: new FormData(formulario) });
    const resultado = await respuesta.json();
    if (!respuesta.ok || resultado.status !== 'success') {
      throw new Error(resultado.message || 'No se pudo registrar el producto');
    }

    const nombreProducto = formulario.elements['nombre_producto'].value.trim().toUpperCase();
    const opcion = new Option(nombreProducto, resultado.codigo_producto, true, true);
    document.getElementById('selProd').add(opcion);
    document.getElementById('selProd').dispatchEvent(new Event('change'));
    formulario.reset();
    closeProductoModal();
    showAlert('El producto fue registrado y quedó seleccionado.', 'success', 'Producto registrado');
      } catch (error) {
    showAlert(error.message || 'No se pudo registrar el producto.', 'error', 'Error');
      }
    });

    function openProveedorModal() {
      document.getElementById('modalProveedorCompra').classList.remove('hidden');
    }

    function closeProveedorModal() {
      document.getElementById('modalProveedorCompra').classList.add('hidden');
    }

    document.getElementById('formProveedorCompra').addEventListener('submit', async function(event) {
      event.preventDefault();

      const formulario = event.currentTarget;
      try {
        const respuesta = await CopiGabyAjax.request(formulario.action, {
          method: 'POST',
          body: new FormData(formulario)
        });
        const resultado = await respuesta.json();
        if (!respuesta.ok || resultado.status !== 'success') {
          throw new Error(resultado.message || 'No se pudo registrar el proveedor');
        }

        const razonSocial = formulario.elements['razon_social'].value.trim().toUpperCase();
        const selector = document.getElementById('selectProveedorCompra');
        const opcion = new Option(razonSocial, resultado.codigo_proveedor, true, true);
        selector.add(opcion);
        selector.dispatchEvent(new Event('change'));
        formulario.reset();
        closeProveedorModal();
        showAlert('El proveedor fue registrado y quedó seleccionado.', 'success', 'Proveedor registrado');
      } catch (error) {
        showAlert(error.message || 'No se pudo registrar el proveedor.', 'error', 'Error');
      }
    });

    function resetSelectFilter(select) {
      window.resetSearchableSelect(select);
    }

    window.attachSearchableSelect(
      document.getElementById('buscarProveedorCompra'),
      document.getElementById('selectProveedorCompra')
    );
    window.attachSearchableSelect(
      document.getElementById('buscarProveedorEdicion'),
      document.getElementById('edit_proveedor')
    );
    window.attachSearchableSelect(
      document.getElementById('buscarProductoCompra'),
      document.getElementById('selProd')
    );
    window.attachSearchableSelect(
      document.getElementById('buscarCategoriaCompra'),
      document.getElementById('selectCategoriaCompra')
    );

    const modal = document.getElementById('modalCompra');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => {
      if (modal.classList.contains('hidden')) {
        resetSelectFilter(document.getElementById('selectProveedorCompra'));
        resetSelectFilter(document.getElementById('selProd'));
      }
      modal.classList.toggle('hidden');
    };
    btnOpen.onclick = toggleModal;

    async function confirmDelete(id) {
      if(await showConfirm('¿Desea anular este registro de compra? Esta acción no se puede deshacer.', 'Anular compra', 'Sí, anular')) {
        const f = new FormData();
        f.append('deleteCompra', 'true');
        f.append('idcompra', id);
        CopiGabyAjax.request('?url=compra&type=main', { method:'POST', body:f })
        .then(r => r.json())
        .then(d => {
          if (d && d.status === 'success') {
            closeDetalleModal();
            setPurchaseEditMode(false);
          }
          showMutationResultAndReload(d, 'Compra anulada correctamente.', 'No se pudo anular la compra.');
        })
        .catch(() => showAlert('No se pudo comunicar con el servidor para anular la compra.', 'error', 'Error de conexión'));
      }
    }

    let detailContext = {};

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function setPurchaseEditMode(enabled) {
      document.querySelectorAll('#tablaDetallesItems [data-purchase-quantity]').forEach(input => {
        input.readOnly = !enabled;
        input.classList.toggle('bg-gray-100', !enabled);
        input.classList.toggle('bg-white', enabled);
      });

      const active = parseInt(detailContext.estado, 10) === 1;
      document.getElementById('detalleCabeceraLectura').classList.toggle('hidden', enabled);
      document.getElementById('detalleCabeceraEdicion').classList.toggle('hidden', !active || !enabled);
      document.getElementById('btnGuardarEdicionCompra').classList.toggle('hidden', !active || !enabled);
      document.getElementById('btnCancelarEdicionCompra').classList.toggle('hidden', !active || !enabled);
      document.getElementById('btnDetalleEditar').classList.toggle('hidden', !active || enabled);
      document.getElementById('btnDetalleEliminar').classList.toggle('hidden', !active || enabled);
      document.getElementById('btnCerrarDetalle').classList.toggle('hidden', enabled);
    }

    function actualizarTotalesDetalleCompra() {
      let total = 0;
      document.querySelectorAll('#tablaDetallesItems [data-purchase-quantity]').forEach(input => {
        const quantity = Number(input.value);
        const cost = Number(input.dataset.purchaseCost);
        const subtotal = Number.isFinite(quantity * cost) ? quantity * cost : 0;
        input.closest('tr').querySelector('[data-purchase-subtotal]').textContent = `$${subtotal.toFixed(2)}`;
        total += subtotal;
      });
      document.getElementById('det_total').textContent = `$${total.toFixed(2)}`;
    }

    async function viewDetails(id) {
      try {
        const response = await CopiGabyAjax.request('?url=compra&type=details', {
          method: 'POST',
          data: { id: id }
        });
        const data = await response.json();
        if (!response.ok || !data.header || !Array.isArray(data.items)) {
          throw new Error('No se pudieron cargar los detalles de la compra.');
        }

          const h = data.header;
          const items = data.items;

          detailContext = {
            id: h.codigo_compra,
            proveedor: h.codigo_proveedor,
            factura: h.numero_factura_proveedor,
            fecha: h.fecha_compra,
            total: h.monto_total,
            estado: h.estado
          };

          document.getElementById('det_factura').innerText = `FACTURA #${h.numero_factura_proveedor}`;
          document.getElementById('det_fecha').innerText = `Fecha de Compra: ${new Date(h.fecha_compra.replace(' ', 'T')).toLocaleDateString('es-VE')}`;
          document.getElementById('det_proveedor').innerText = h.razon_social;
          document.getElementById('det_rif').innerText = `RIF: ${h.rif_proveedor} | Tel: ${h.telefono}`;
          document.getElementById('det_estado').innerText = Number(h.estado) === 1 ? 'Compra Activa' : 'Compra Anulada';
          document.getElementById('det_usuario').innerText = h.nombre_usuario;
          document.getElementById('det_total').innerText = `$${parseFloat(h.monto_total).toFixed(2)}`;
          resetSelectFilter(document.getElementById('edit_proveedor'));
          document.getElementById('edit_proveedor').value = h.codigo_proveedor;
          window.syncSearchableSelect(document.getElementById('edit_proveedor'));
          document.getElementById('edit_factura').value = h.numero_factura_proveedor;
          document.getElementById('edit_fecha').value = h.fecha_compra;
          document.getElementById('edit_estado').checked = Number(h.estado) === 1;

          const tbody = document.querySelector('#tablaDetallesItems tbody');
          tbody.innerHTML = '';
          items.forEach(it => {
            const row = document.createElement('tr');
            const name = document.createElement('td');
            name.className = 'px-4 py-3 uppercase';
            name.textContent = it.nombre_producto;

            const quantityCell = document.createElement('td');
            quantityCell.className = 'px-4 py-3 text-center';
            const quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.min = '1';
            quantityInput.step = '1';
            quantityInput.value = String(it.cantidad);
            quantityInput.readOnly = true;
            quantityInput.dataset.purchaseQuantity = String(it.codigo_detalle_compra);
            quantityInput.dataset.purchaseCost = String(it.costo_unitario);
            quantityInput.setAttribute('aria-label', `Cantidad de ${it.nombre_producto}`);
            quantityInput.className = 'w-24 rounded border bg-gray-100 px-2 py-1 text-center';
            quantityInput.addEventListener('input', actualizarTotalesDetalleCompra);
            quantityCell.appendChild(quantityInput);

            const cost = document.createElement('td');
            cost.className = 'px-4 py-3 text-right';
            cost.textContent = `$${parseFloat(it.costo_unitario).toFixed(2)}`;
            const subtotal = document.createElement('td');
            subtotal.className = 'px-4 py-3 text-right font-black text-orange-dk';
            subtotal.dataset.purchaseSubtotal = '';
            subtotal.textContent = `$${parseFloat(it.subtotal).toFixed(2)}`;

            row.append(name, quantityCell, cost, subtotal);
            tbody.appendChild(row);
          });

          document.getElementById('btnDetalleEditar').onclick = () => setPurchaseEditMode(true);
          document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(detailContext.id);
          document.getElementById('btnCancelarEdicionCompra').onclick = () => viewDetails(detailContext.id);
          document.getElementById('btnGuardarEdicionCompra').onclick = guardarEdicionCompra;
          setPurchaseEditMode(false);
          document.getElementById('modalDetalle').classList.remove('hidden');
          return true;
      } catch (error) {
        showAlert(error.message || 'No se pudieron cargar los detalles de la compra.', 'error', 'Error');
        return false;
      }
    }

    async function guardarEdicionCompra() {
      if (window.CopiGabyValidaciones && !window.CopiGabyValidaciones.validarEdicionCompra()) return;

      const inputs = [...document.querySelectorAll('#tablaDetallesItems [data-purchase-quantity]')];
      const items = inputs.map(input => ({
        codigo_detalle_compra: Number(input.dataset.purchaseQuantity),
        cantidad: Number(input.value)
      }));
      const invalidInput = inputs.find(input => !Number.isInteger(Number(input.value)) || Number(input.value) < 1);
      if (invalidInput) {
        invalidInput.focus();
        showAlert('Todas las cantidades deben ser números enteros mayores a cero.', 'warning', 'Cantidad no válida');
        return;
      }

      const proveedor = document.getElementById('edit_proveedor');
      const factura = document.getElementById('edit_factura');
      const fecha = document.getElementById('edit_fecha');
      if (!proveedor.value) {
        showAlert('Seleccione un proveedor para la compra.', 'warning', 'Proveedor requerido');
        return;
      }
      if (!factura.value.trim()) {
        factura.focus();
        showAlert('Indique el número de factura.', 'warning', 'Factura requerida');
        return;
      }
      if (!fecha.value || !fecha.checkValidity()) {
        fecha.reportValidity();
        return;
      }

      const idCompra = detailContext.id;
      const button = document.getElementById('btnGuardarEdicionCompra');
      button.disabled = true;
      const originalText = button.textContent;
      button.textContent = 'Guardando...';
      try {
        const formData = new FormData();
        formData.append('idcompra', String(idCompra));
        formData.append('items', JSON.stringify(items));
        formData.append('codigo_proveedor', proveedor.value);
        formData.append('numero_factura_proveedor', factura.value.trim());
        formData.append('fecha_compra', fecha.value);
        formData.append('estado', document.getElementById('edit_estado').checked ? '1' : '0');
        const response = await CopiGabyAjax.request('?url=compra&type=update_details', { method: 'POST', body: formData });
        const result = await response.json();
        if (!response.ok || result.status !== 'success') {
          throw new Error(result.message || 'No se pudo actualizar la compra.');
        }

        await showAlert(result.message, 'success', 'Compra actualizada');
        closeDetalleModal();
        setPurchaseEditMode(false);
        window.CopiGabyAjax.refresh();
      } catch (error) {
        showAlert(error.message || 'No se pudo actualizar la compra.', 'error', 'Error');
      } finally {
        button.disabled = false;
        button.textContent = originalText;
      }
    }

    // Lógica dinámica para gestionar los productos de la compra
    let items = [];
    let total = 0;

    // Al seleccionar un producto, cargar su último costo registrado automáticamente
    document.getElementById('selProd').onchange = function() {
        const opt = this.options[this.selectedIndex];
        if(opt.value) document.getElementById('costProd').value = opt.dataset.costo;
    };

    function addItem() {
        const sel = document.getElementById('selProd');
        const cantInput = document.getElementById('cantProd');
        const costInput = document.getElementById('costProd');
        
        const cant = Number(cantInput.value);
        const cost = Number(costInput.value);

        if(!sel.value || !Number.isInteger(cant) || cant <= 0 || !Number.isFinite(cost) || cost <= 0 || !costInput.validity.valid) {
            showAlert('Seleccione un producto e indique una cantidad entera positiva y un costo positivo con hasta dos decimales.', 'warning', 'Datos incompletos');
            return;
        }

        const costoNormalizado = Math.round(cost * 100) / 100;

        // Agregar al arreglo de items
        items.push({
            codigo_producto: sel.value,
            nombre: sel.options[sel.selectedIndex].text,
            cantidad: cant,
            costo: costoNormalizado
        });

        cantInput.value = "1";
        sel.value = '';
        window.syncSearchableSelect(sel);
        costInput.value = '';
        
        renderTable();
    }

    function renderTable() {
        const body = document.querySelector('#tablaTemp tbody');
        body.innerHTML = '';
        total = 0;
        items.forEach((it, index) => {
            const sub = Math.round(it.cantidad * it.costo * 100) / 100;
            total = Math.round((total + sub) * 100) / 100;
            body.innerHTML += `<tr><td class="px-4 py-2">${it.nombre}</td><td class="px-4 py-2"><input type="number" min="1" step="1" required value="${it.cantidad}" data-item-quantity="${index}" aria-label="Cantidad de ${it.nombre}" class="w-20 rounded border px-2 py-1 text-center"></td><td class="px-4 py-2">$${it.costo.toFixed(2)}</td><td data-item-subtotal class="px-4 py-2 text-right font-black">$${sub.toFixed(2)}</td></tr>`;
        });
        document.getElementById('txtTotal').innerText = `$${total.toFixed(2)}`;
    }

    const tablaItemsCompra = document.querySelector('#tablaTemp tbody');
    tablaItemsCompra.addEventListener('input', function(event) {
      const input = event.target.closest('[data-item-quantity]');
      if (!input) return;
      const index = Number(input.dataset.itemQuantity);
      const quantity = Number(input.value);
      if (!Number.isInteger(quantity) || quantity <= 0 || !items[index]) return;

      items[index].cantidad = quantity;
      const subtotal = Math.round(quantity * items[index].costo * 100) / 100;
      input.closest('tr').querySelector('[data-item-subtotal]').textContent = `$${subtotal.toFixed(2)}`;
      total = Math.round(items.reduce((sum, item) => sum + item.cantidad * item.costo, 0) * 100) / 100;
      document.getElementById('txtTotal').innerText = `$${total.toFixed(2)}`;
    });

    tablaItemsCompra.addEventListener('change', function(event) {
      const input = event.target.closest('[data-item-quantity]');
      if (!input) return;
      const index = Number(input.dataset.itemQuantity);
      if (!Number.isInteger(Number(input.value)) || Number(input.value) <= 0) {
        input.value = String(items[index].cantidad);
      }
    });

    async function submitCompra() {
        if(items.length === 0) {
            showAlert('No puede registrar una compra sin productos. Agregue al menos uno a la lista.', 'warning', 'Compra sin productos');
            return;
        }

        const formulario = document.getElementById('formCompra');
        if (!formulario.checkValidity()) {
            formulario.reportValidity();
            return;
        }

        document.getElementById('inputItems').value = JSON.stringify(items);
        document.getElementById('inputTotal').value = total.toFixed(2);

        const boton = document.getElementById('btnRegistrarCompra');
        boton.disabled = true;
        boton.textContent = 'REGISTRANDO...';

        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 15000);

        let resultado;
        try {
            const respuesta = await CopiGabyAjax.request(formulario.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(formulario),
                signal: controller.signal
            });

            resultado = await respuesta.json();
            if (!respuesta.ok || resultado.status !== 'success') {
                throw new Error(resultado.message || 'No se pudo registrar la compra');
            }
        } catch (error) {
            showAlert(
              error.name === 'AbortError'
                ? 'El registro tardó demasiado tiempo. Revisa si la compra se registró antes de intentar nuevamente.'
                : error.message,
              'error',
              error.name === 'AbortError' ? 'Tiempo agotado' : 'Error al registrar'
            );
            return;
        } finally {
            clearTimeout(timeout);
            boton.disabled = false;
            boton.textContent = 'REGISTRAR COMPRA';
        }

        formulario.reset();
        items = [];
        renderTable();
        modal.classList.add('hidden');
        await showAlert(resultado.message || 'La compra fue registrada correctamente.', 'success', 'Compra registrada');

        try {
            await CopiGabyAjax.refresh();
        } catch (error) {
            console.error('La compra se registró, pero no se pudo actualizar la lista.', error);
            showAlert('La compra se registró, pero no se pudo actualizar la lista. Actualice la vista manualmente.', 'warning', 'Compra registrada');
        }
    }

    function escapeTableText(value) {
      return String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
      })[character]);
    }

    function formatPurchaseDate(value) {
      const parts = String(value || '').slice(0, 10).split('-');
      return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : String(value || '');
    }

    window.CopiGabyDataTablesConfig = window.CopiGabyDataTablesConfig || {};
    window.CopiGabyDataTablesConfig.compras = {
      serverSide: true,
      processing: true,
      searchDelay: 350,
      ajax: {
        url: '?url=compra&type=list',
        type: 'POST',
        dataType: 'json'
      },
      pageLength: 10,
      lengthMenu: [[10, 25, 50], [10, 25, 50]],
      order: [[0, 'desc']],
      columns: [
        {
          data: 'codigo_compra',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '<span class="text-gray-400 font-bold">#' + escapeTableText(data) + '</span>';
          }
        },
        {
          data: 'numero_factura_proveedor',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '<span class="font-black text-navy-dark">' + escapeTableText(data || '—') + '</span>';
          }
        },
        {
          data: 'nombre_proveedor',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '<span class="uppercase text-[0.8rem]">' + escapeTableText(data) + '</span>';
          }
        },
        {
          data: 'fecha_compra',
          render: function (data, type) {
            return type === 'display' ? escapeTableText(formatPurchaseDate(data)) : data;
          }
        },
        {
          data: 'monto_total',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '<span class="font-black text-navy-dark text-[1rem]">$'
              + (Number(data) || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
              }) + '</span>';
          }
        },
        {
          data: 'estado',
          render: function (data, type) {
            if (type !== 'display') return data;
            const activa = Number(data) === 1;
            return '<span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase '
              + (activa ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') + '">'
              + (activa ? 'Activa' : 'Inactiva') + '</span>';
          }
        },
        {
          data: 'codigo_compra',
          orderable: false,
          searchable: false,
          render: function () {
            return '<button type="button" class="js-view-purchase-details group relative text-orange-dk p-2 hover:bg-orange/10 rounded-lg transition-colors">'
              + '<span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Consultar</span>'
              + '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'
              + '</button>';
          }
        }
      ],
      initComplete: function () {
        const api = this.api();
        window.jQuery(api.table().body()).on('click', '.js-view-purchase-details', function () {
          const compra = api.row(window.jQuery(this).closest('tr')).data();
          if (compra) viewDetails(compra.codigo_compra);
        });
      }
    };

    if (typeof openProductoModal === "function") window.openProductoModal = openProductoModal;
    if (typeof closeProductoModal === "function") window.closeProductoModal = closeProductoModal;
    if (typeof openProveedorModal === "function") window.openProveedorModal = openProveedorModal;
    if (typeof closeProveedorModal === "function") window.closeProveedorModal = closeProveedorModal;
    if (typeof resetSelectFilter === "function") window.resetSelectFilter = resetSelectFilter;
    if (typeof toggleModal === "function") window.toggleModal = toggleModal;
    if (typeof confirmDelete === "function") window.confirmDelete = confirmDelete;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof setPurchaseEditMode === "function") window.setPurchaseEditMode = setPurchaseEditMode;
    if (typeof actualizarTotalesDetalleCompra === "function") window.actualizarTotalesDetalleCompra = actualizarTotalesDetalleCompra;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof guardarEdicionCompra === "function") window.guardarEdicionCompra = guardarEdicionCompra;
    if (typeof addItem === "function") window.addItem = addItem;
    if (typeof renderTable === "function") window.renderTable = renderTable;
    if (typeof submitCompra === "function") window.submitCompra = submitCompra;
  });
})(window, document, window.jQuery);
