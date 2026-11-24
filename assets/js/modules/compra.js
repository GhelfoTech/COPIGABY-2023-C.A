/**
 * Módulo de gestión de compras a proveedores.
 */
(function (window, document, $) {
  'use strict';

  $(function () {
    const ReglasCompra = {
      factura: {
        regex: /^[A-Za-z0-9\-\#\/]{1,25}$/,
        errorMsg: 'El número de factura solo permite caracteres alfanuméricos, guiones y barras (1 a 25 caracteres).'
      },
      fecha: {
        regex: /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/,
        errorMsg: 'La fecha de compra debe tener un formato ISO 8601 válido (AAAA-MM-DD).'
      }
    };

    const ReglasProducto = {
      nombre: {
        regex: /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s.,#\/-]{3,100}$/u,
        errorMsg: 'El nombre del producto debe contener entre 3 y 100 caracteres alfanuméricos válidos.'
      },
      porcentaje: {
        regex: /^[1-9]\d{0,2}$/,
        errorMsg: 'El porcentaje de ganancia debe ser un número entero mayor a cero (ej: 25).'
      },
      stock: {
        regex: /^\d+$/,
        errorMsg: 'El stock mínimo debe ser un número entero mayor o igual a cero.'
      }
    };

    const $modalCompra = $('#modalCompra');
    const $modalProducto = $('#modalProductoCompra');
    const $modalProveedor = $('#modalProveedorCompra');
    const $modalDetalle = $('#modalDetalle');
    const $tablaCompras = $('#tablaCompras');
    const $btnOpenModal = $('#btnOpenModal');

    let items = [];
    let total = 0;
    let detailContext = {};

    function resetSelectFilter(selectElement) {
      if (selectElement && window.resetSearchableSelect) {
        window.resetSearchableSelect(selectElement);
      }
    }

    if (window.attachSearchableSelect) {
      window.attachSearchableSelect($('#buscarProveedorCompra')[0], $('#selectProveedorCompra')[0]);
      window.attachSearchableSelect($('#buscarProveedorEdicion')[0], $('#edit_proveedor')[0]);
      window.attachSearchableSelect($('#buscarProductoCompra')[0], $('#selProd')[0]);
      window.attachSearchableSelect($('#buscarCategoriaCompra')[0], $('#selectCategoriaCompra')[0]);
    }

    function toggleModal() {
      $modalCompra.toggleClass('hidden');
      if (!$modalCompra.hasClass('hidden')) {
        resetSelectFilter($('#selectProveedorCompra')[0]);
        resetSelectFilter($('#selProd')[0]);
      }
    }

    function openProductoModal() {
      resetSelectFilter($('#selectCategoriaCompra')[0]);
      $modalProducto.removeClass('hidden');
    }

    function closeProductoModal() {
      $modalProducto.addClass('hidden');
    }

    function openProveedorModal() {
      $modalProveedor.removeClass('hidden');
    }

    function closeProveedorModal() {
      $modalProveedor.addClass('hidden');
    }

    function closeDetalleModal() {
      $modalDetalle.addClass('hidden');
    }

    $btnOpenModal.on('click', toggleModal);
    // Los botones con data-js-action="toggleModal" los maneja layout.js (window.toggleModal);
    // un segundo listener aquí haría un doble toggle y el modal no se cerraría.
    $(document).on('click', '[data-js-action="openProveedorModal"]', openProveedorModal);
    $(document).on('click', '[data-js-action="closeProveedorModal"]', closeProveedorModal);
    $(document).on('click', '[data-js-action="openProductoModal"]', openProductoModal);
    $(document).on('click', '[data-js-action="closeProductoModal"]', closeProductoModal);
    $(document).on('click', '[data-js-action="closeDetalleModal"]', closeDetalleModal);

    // Registro rápido de producto desde modal de compra
    $('#formProductoCompra').on('submit', function (e) {
      e.preventDefault();
      const $form = $(this);
      const nombre = $.trim($form.find('[name="nombre_producto"]').val() || '');
      const porcentaje = $.trim($form.find('[name="porcentaje_ganancia"]').val() || '');
      const stockMin = $.trim($form.find('[name="stock_minimo"]').val() || '');

      if (!ReglasProducto.nombre.regex.test(nombre)) {
        showAlert(ReglasProducto.nombre.errorMsg, 'warning', 'Nombre Inválido');
        $form.find('[name="nombre_producto"]').focus();
        return;
      }
      if (porcentaje !== '' && !ReglasProducto.porcentaje.regex.test(porcentaje)) {
        showAlert(ReglasProducto.porcentaje.errorMsg, 'warning', 'Porcentaje Inválido');
        $form.find('[name="porcentaje_ganancia"]').focus();
        return;
      }
      if (stockMin !== '' && !ReglasProducto.stock.regex.test(stockMin)) {
        showAlert(ReglasProducto.stock.errorMsg, 'warning', 'Stock Inválido');
        $form.find('[name="stock_minimo"]').focus();
        return;
      }

      const formData = new FormData(this);
      formData.append('origen', 'compra');

      $.ajax({
        url: $form.attr('action') || '?url=producto&type=register',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (resultado) {
          if (resultado && resultado.status === 'success') {
            const nombreUpper = nombre.toUpperCase();
            const opcion = new Option(nombreUpper, resultado.codigo_producto, true, true);
            $('#selProd').append(opcion).trigger('change');
            $form[0].reset();
            closeProductoModal();
            showAlert('El producto fue registrado y quedó seleccionado.', 'success', 'Producto Registrado');
          } else {
            showAlert((resultado && resultado.message) || 'No se pudo registrar el producto.', 'error', 'Error');
          }
        },
        error: function () {
          showAlert('No se pudo comunicar con el servidor para registrar el producto.', 'error', 'Error de Red');
        }
      });
    });

    // Registro rápido de proveedor desde modal de compra
    $('#formProveedorCompra').on('submit', function (e) {
      e.preventDefault();
      const $form = $(this);
      const razonSocial = $.trim($form.find('[name="razon_social"]').val() || '');

      if (!razonSocial) {
        showAlert('La razón social del proveedor es obligatoria.', 'warning', 'Proveedor Requerido');
        $form.find('[name="razon_social"]').focus();
        return;
      }

      const formData = new FormData(this);

      $.ajax({
        url: $form.attr('action') || '?url=proveedor&type=register',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (resultado) {
          if (resultado && resultado.status === 'success') {
            const razonUpper = razonSocial.toUpperCase();
            const opcion = new Option(razonUpper, resultado.codigo_proveedor, true, true);
            $('#selectProveedorCompra').append(opcion).trigger('change');
            $form[0].reset();
            closeProveedorModal();
            showAlert('El proveedor fue registrado y quedó seleccionado.', 'success', 'Proveedor Registrado');
          } else {
            showAlert((resultado && resultado.message) || 'No se pudo registrar el proveedor.', 'error', 'Error');
          }
        },
        error: function () {
          showAlert('No se pudo comunicar con el servidor para registrar el proveedor.', 'error', 'Error de Red');
        }
      });
    });

    $('#selProd').on('change', function () {
      const selectedOption = $(this).find('option:selected');
      const costo = selectedOption.data('costo');
      if (selectedOption.val() && costo !== undefined) {
        $('#costProd').val(costo);
      }
    });

    function addItem() {
      const $sel = $('#selProd');
      const $cantInput = $('#cantProd');
      const $costInput = $('#costProd');

      const prodId = $sel.val();
      const cant = Number($cantInput.val());
      const cost = Number($costInput.val());

      if (!prodId || !Number.isFinite(cant) || cant <= 0 || !Number.isFinite(cost) || cost <= 0) {
        showAlert('Seleccione un producto e indique una cantidad positiva y un costo válido (se permiten decimales).', 'warning', 'Datos Incompletos');
        return;
      }

      const costoNormalizado = Math.round(cost * 100) / 100;
      const nombreProd = $sel.find('option:selected').text();

      items.push({
        codigo_producto: prodId,
        nombre: nombreProd,
        cantidad: cant,
        costo: costoNormalizado
      });

      $cantInput.val('1');
      $sel.val('');
      if (window.syncSearchableSelect) window.syncSearchableSelect($sel[0]);
      $costInput.val('');

      renderTable();
    }

    function renderTable() {
      const $tbody = $('#tablaTemp tbody');
      $tbody.empty();
      total = 0;

      items.forEach(function (it, index) {
        const sub = Math.round(it.cantidad * it.costo * 100) / 100;
        total = Math.round((total + sub) * 100) / 100;

        const $tr = $('<tr>');
        $tr.append($('<td>').addClass('px-4 py-2 font-bold text-gray-700').text(it.nombre));

        const $qtyInput = $('<input>')
          .attr({ type: 'number', min: '1', step: '1', 'aria-label': `Cantidad de ${it.nombre}` })
          .val(it.cantidad)
          .attr('data-item-quantity', index)
          .addClass('w-20 rounded border px-2 py-1 text-center font-bold');

        $tr.append($('<td>').addClass('px-4 py-2').append($qtyInput));
        $tr.append($('<td>').addClass('px-4 py-2 font-bold').text('$' + it.costo.toFixed(2)));
        $tr.append($('<td>').addClass('px-4 py-2 text-right font-black text-orange-dk').attr('data-item-subtotal', '').text('$' + sub.toFixed(2)));

        const $deleteBtn = $('<button>')
          .attr({ type: 'button', title: 'Quitar renglón' })
          .addClass('text-red-500 hover:text-red-700 font-bold px-2 py-1')
          .text('✕')
          .on('click', function () {
            items.splice(index, 1);
            renderTable();
          });

        $tr.append($('<td>').addClass('px-4 py-2 text-center').append($deleteBtn));
        $tbody.append($tr);
      });

      $('#txtTotal').text('$' + total.toFixed(2));
    }

    $('#tablaTemp tbody').on('input', '[data-item-quantity]', function () {
      const index = Number($(this).data('item-quantity'));
      const quantity = Number($(this).val());
      if (!Number.isFinite(quantity) || quantity <= 0 || !items[index]) return;

      items[index].cantidad = quantity;
      const subtotal = Math.round(quantity * items[index].costo * 100) / 100;
      $(this).closest('tr').find('[data-item-subtotal]').text('$' + subtotal.toFixed(2));

      total = Math.round(items.reduce((sum, it) => sum + (it.cantidad * it.costo), 0) * 100) / 100;
      $('#txtTotal').text('$' + total.toFixed(2));
    });

    $('#tablaTemp tbody').on('change', '[data-item-quantity]', function () {
      const index = Number($(this).data('item-quantity'));
      if (!Number.isFinite(Number($(this).val())) || Number($(this).val()) <= 0) {
        $(this).val(String(items[index].cantidad));
      }
    });

    // Envío y registro de compra
    function submitCompra() {
      if (items.length === 0) {
        showAlert('No puede registrar una compra sin productos. Agregue al menos un producto a la lista.', 'warning', 'Compra Vacía');
        return;
      }

      const $form = $('#formCompra');
      const proveedorVal = $form.find('[name="codigo_proveedor"]').val();
      const facturaVal = $.trim($form.find('[name="numero_factura_proveedor"]').val() || '');
      const fechaVal = $.trim($form.find('[name="fecha_compra"]').val() || '');

      if (!proveedorVal) {
        showAlert('Debe seleccionar un proveedor.', 'warning', 'Proveedor Requerido');
        return;
      }

      if (!facturaVal || !ReglasCompra.factura.regex.test(facturaVal)) {
        showAlert(ReglasCompra.factura.errorMsg, 'warning', 'Factura Inválida');
        $form.find('[name="numero_factura_proveedor"]').focus();
        return;
      }

      if (!fechaVal || !ReglasCompra.fecha.regex.test(fechaVal)) {
        showAlert(ReglasCompra.fecha.errorMsg, 'warning', 'Fecha Inválida');
        $form.find('[name="fecha_compra"]').focus();
        return;
      }

      $('#inputItems').val(JSON.stringify(items));
      $('#inputTotal').val(total.toFixed(2));

      const $btn = $('#btnRegistrarCompra');
      $btn.prop('disabled', true).text('Registrando...');

      const formData = new FormData($form[0]);

      $.ajax({
        url: $form.attr('action') || '?url=compra&type=register',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (resultado) {
          if (resultado && resultado.status === 'success') {
            $form[0].reset();
            items = [];
            renderTable();
            $modalCompra.addClass('hidden');
            showAlert(resultado.message || 'La compra fue registrada exitosamente.', 'success', 'Compra Registrada');
            if ($.fn.DataTable.isDataTable($tablaCompras)) {
              $tablaCompras.DataTable().ajax.reload(null, false);
            }
          } else {
            showAlert((resultado && resultado.message) || 'No se pudo registrar la compra.', 'error', 'Error');
          }
        },
        error: function (xhr, status, error) {
          console.error('[Error AJAX Compra Register]', error);
          showAlert('Error de comunicación con el servidor al registrar la compra.', 'error', 'Error de Red');
        },
        complete: function () {
          $btn.prop('disabled', false).text('Registrar Compra');
        }
      });
    }

    $('#btnRegistrarCompra').on('click', function (e) {
      e.preventDefault();
      submitCompra();
    });

    $('#btnAddItem').on('click', function (e) {
      e.preventDefault();
      addItem();
    });

    // Consulta de detalles de compra
    function viewDetails(id) {
      $.ajax({
        url: '?url=compra&type=details',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function (data) {
          if (!data || !data.header || !Array.isArray(data.items)) {
            showAlert('No se pudieron cargar los detalles de la compra.', 'error');
            return;
          }

          const h = data.header;
          const itemsData = data.items;

          detailContext = {
            id: h.codigo_compra,
            proveedor: h.codigo_proveedor,
            factura: h.numero_factura_proveedor,
            fecha: h.fecha_compra,
            total: h.monto_total,
            estado: h.estado
          };

          $('#det_factura').text(`FACTURA #${h.numero_factura_proveedor}`);
          $('#det_fecha').text(`Fecha de Compra: ${new Date(h.fecha_compra.replace(' ', 'T')).toLocaleDateString('es-VE')}`);
          $('#det_proveedor').text(h.razon_social);
          $('#det_rif').text(`RIF: ${h.rif_proveedor} | Tel: ${h.telefono}`);
          $('#det_estado').text(Number(h.estado) === 1 ? 'Compra Activa' : 'Compra Anulada');
          $('#det_usuario').text(h.nombre_usuario);
          $('#det_total').text(`$${parseFloat(h.monto_total).toFixed(2)}`);

          resetSelectFilter($('#edit_proveedor')[0]);
          $('#edit_proveedor').val(h.codigo_proveedor);
          if (window.syncSearchableSelect) window.syncSearchableSelect($('#edit_proveedor')[0]);
          $('#edit_factura').val(h.numero_factura_proveedor);
          $('#edit_fecha').val(h.fecha_compra);
          $('#edit_estado').prop('checked', Number(h.estado) === 1);

          const $tbody = $('#tablaDetallesItems tbody');
          $tbody.empty();

          itemsData.forEach(function (it) {
            const $row = $('<tr>');
            $row.append($('<td>').addClass('px-4 py-3 uppercase font-bold text-navy-dark').text(it.nombre_producto));

            const $quantityInput = $('<input>')
              .attr({
                type: 'number',
                min: '0.01',
                step: '0.01',
                'aria-label': `Cantidad de ${it.nombre_producto}`,
                'data-purchase-quantity': String(it.codigo_detalle_compra),
                'data-purchase-cost': String(it.costo_unitario)
              })
              .val(String(it.cantidad))
              .prop('readOnly', true)
              .addClass('w-24 rounded border bg-gray-100 px-2 py-1 text-center font-bold')
              .on('input', actualizarTotalesDetalleCompra);

            $row.append($('<td>').addClass('px-4 py-3 text-center').append($quantityInput));
            $row.append($('<td>').addClass('px-4 py-3 text-right font-bold').text(`$${parseFloat(it.costo_unitario).toFixed(2)}`));
            $row.append($('<td>').addClass('px-4 py-3 text-right font-black text-orange-dk').attr('data-purchase-subtotal', '').text(`$${parseFloat(it.subtotal).toFixed(2)}`));

            $tbody.append($row);
          });

          $('#btnDetalleEditar').off('click').on('click', () => setPurchaseEditMode(true));
          $('#btnDetalleEliminar').off('click').on('click', () => confirmDelete(detailContext.id));
          $('#btnCancelarEdicionCompra').off('click').on('click', () => viewDetails(detailContext.id));
          $('#btnGuardarEdicionCompra').off('click').on('click', guardarEdicionCompra);

          setPurchaseEditMode(false);
          $modalDetalle.removeClass('hidden');
        },
        error: function () {
          showAlert('No se pudieron consultar los detalles de la compra.', 'error', 'Error de Conexión');
        }
      });
    }

    function setPurchaseEditMode(enabled) {
      $('#tablaDetallesItems [data-purchase-quantity]').each(function () {
        $(this)
          .prop('readOnly', !enabled)
          .toggleClass('bg-gray-100', !enabled)
          .toggleClass('bg-white', enabled);
      });

      const active = parseInt(detailContext.estado, 10) === 1;
      $('#detalleCabeceraLectura').toggleClass('hidden', enabled);
      $('#detalleCabeceraEdicion').toggleClass('hidden', !active || !enabled);
      $('#btnGuardarEdicionCompra').toggleClass('hidden', !active || !enabled);
      $('#btnCancelarEdicionCompra').toggleClass('hidden', !active || !enabled);
      $('#btnDetalleEditar').toggleClass('hidden', !active || enabled);
      $('#btnDetalleEliminar').toggleClass('hidden', !active || enabled);
      $('#btnCerrarDetalle').toggleClass('hidden', enabled);
    }

    function actualizarTotalesDetalleCompra() {
      let sum = 0;
      $('#tablaDetallesItems [data-purchase-quantity]').each(function () {
        const $input = $(this);
        const quantity = Number($input.val());
        const cost = Number($input.attr('data-purchase-cost'));
        const subtotal = Number.isFinite(quantity * cost) ? quantity * cost : 0;
        $input.closest('tr').find('[data-purchase-subtotal]').text(`$${subtotal.toFixed(2)}`);
        sum += subtotal;
      });
      $('#det_total').text(`$${sum.toFixed(2)}`);
    }

    function guardarEdicionCompra() {
      const inputs = $('#tablaDetallesItems [data-purchase-quantity]').toArray();
      const updatedItems = inputs.map(input => ({
        codigo_detalle_compra: Number($(input).attr('data-purchase-quantity')),
        cantidad: Number($(input).val())
      }));

      const invalidInput = inputs.find(input => !Number.isFinite(Number($(input).val())) || Number($(input).val()) < 1);
      if (invalidInput) {
        $(invalidInput).focus();
        showAlert('Todas las cantidades deben ser números mayores a cero (se permiten decimales).', 'warning', 'Cantidad No Válida');
        return;
      }

      const proveedor = $('#edit_proveedor').val();
      const factura = $.trim($('#edit_factura').val() || '');
      const fecha = $('#edit_fecha').val();

      if (!proveedor) {
        showAlert('Seleccione un proveedor para la compra.', 'warning', 'Proveedor Requerido');
        return;
      }
      if (!factura || !ReglasCompra.factura.regex.test(factura)) {
        $('#edit_factura').focus();
        showAlert(ReglasCompra.factura.errorMsg, 'warning', 'Factura Inválida');
        return;
      }
      if (!fecha || !ReglasCompra.fecha.regex.test(fecha)) {
        $('#edit_fecha').focus();
        showAlert(ReglasCompra.fecha.errorMsg, 'warning', 'Fecha Inválida');
        return;
      }

      const $btn = $('#btnGuardarEdicionCompra');
      $btn.prop('disabled', true).text('Guardando...');

      $.ajax({
        url: '?url=compra&type=update_details',
        type: 'POST',
        data: {
          idcompra: detailContext.id,
          items: JSON.stringify(updatedItems),
          codigo_proveedor: proveedor,
          numero_factura_proveedor: factura,
          fecha_compra: fecha,
          estado: $('#edit_estado').is(':checked') ? '1' : '0'
        },
        dataType: 'json',
        success: function (result) {
          if (result && result.status === 'success') {
            showAlert(result.message || 'Compra actualizada correctamente.', 'success', 'Actualizado');
            closeDetalleModal();
            setPurchaseEditMode(false);
            if ($.fn.DataTable.isDataTable($tablaCompras)) {
              $tablaCompras.DataTable().ajax.reload(null, false);
            }
          } else {
            showAlert((result && result.message) || 'No se pudo actualizar la compra.', 'error', 'Error');
          }
        },
        error: function () {
          showAlert('No se pudo actualizar la compra en el servidor.', 'error', 'Error de Conexión');
        },
        complete: function () {
          $btn.prop('disabled', false).text('Guardar Cambios');
        }
      });
    }

    function confirmDelete(id) {
      showConfirm('¿Desea anular este registro de compra? Esta acción no se puede deshacer.', 'Anular compra', 'Sí, anular')
        .then(function (confirmed) {
          if (!confirmed) return;

          $.ajax({
            url: '?url=compra&type=main',
            type: 'POST',
            data: { deleteCompra: 'true', idcompra: id },
            dataType: 'json',
            success: function (d) {
              if (d && d.status === 'success') {
                closeDetalleModal();
                setPurchaseEditMode(false);
                showAlert(d.message || 'Compra anulada correctamente.', 'success');
                if ($.fn.DataTable.isDataTable($tablaCompras)) {
                  $tablaCompras.DataTable().ajax.reload(null, false);
                }
              } else {
                showAlert((d && d.message) || 'No se pudo anular la compra.', 'error');
              }
            },
            error: function () {
              showAlert('No se pudo comunicar con el servidor para anular la compra.', 'error', 'Error de Conexión');
            }
          });
        });
    }

    function escapeTableText(value) {
      return String(value ?? '').replace(/[&<>"']/g, function (ch) {
        return {
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot;',
          "'": '&#39;'
        }[ch];
      });
    }

    function formatPurchaseDate(value) {
      if (!value) return '—';
      const parsed = new Date(String(value).replace(' ', 'T'));
      return Number.isNaN(parsed.getTime()) ? String(value) : parsed.toLocaleDateString('es-VE');
    }

    // Configuración DataTables Server-Side
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
      columns: [
        {
          data: 'codigo_compra',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '<span class="font-bold text-gray-500">#' + escapeTableText(String(data).padStart(4, '0')) + '</span>';
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
        $(api.table().body()).on('click', '.js-view-purchase-details', function () {
          const compra = api.row($(this).closest('tr')).data();
          if (compra) viewDetails(compra.codigo_compra);
        });
      }
    };

    window.openProductoModal = openProductoModal;
    window.closeProductoModal = closeProductoModal;
    window.openProveedorModal = openProveedorModal;
    window.closeProveedorModal = closeProveedorModal;
    window.resetSelectFilter = resetSelectFilter;
    window.toggleModal = toggleModal;
    window.confirmDelete = confirmDelete;
    window.closeDetalleModal = closeDetalleModal;
    window.setPurchaseEditMode = setPurchaseEditMode;
    window.actualizarTotalesDetalleCompra = actualizarTotalesDetalleCompra;
    window.viewDetails = viewDetails;
    window.guardarEdicionCompra = guardarEdicionCompra;
    window.addItem = addItem;
    window.renderTable = renderTable;
    window.submitCompra = submitCompra;
  });
})(window, document, window.jQuery);
