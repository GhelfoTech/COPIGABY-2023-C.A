/**
 * Módulo de gestión de productos e insumos.
 */
(function (window, document, $) {
  'use strict';

  $(function () {
    const $modal = $('#modalProduct');
    const $btnOpen = $('#btnOpenModal');
    const $formRegister = $('#formProductoRegister');
    const $modalEdit = $('#modalEditProduct');
    const $formEdit = $('#modalEditProduct form');
    const $modalDetalle = $('#modalDetalle');
    const $tablaProductos = $('#tablaProductos');

    let currentRecord = null;

    function toggleModal() {
      $modal.toggleClass('hidden');
      if (!$modal.hasClass('hidden')) {
        resetCategoryFilter();
      }
    }

    function closeEditModal() {
      $modalEdit.addClass('hidden');
    }

    function closeDetalleModal() {
      $modalDetalle.addClass('hidden');
    }

    function resetCategoryFilter() {
      const select = $('#selectCategoriaProducto')[0];
      if (select && window.resetSearchableSelect) {
        window.resetSearchableSelect(select);
      }
    }

    if (window.attachSearchableSelect) {
      window.attachSearchableSelect(
        $('#buscarCategoriaProducto')[0],
        $('#selectCategoriaProducto')[0]
      );
    }

    $btnOpen.on('click', toggleModal);
    $(document).on('click', '.closeModal, #overlay', function () {
      $modal.addClass('hidden');
    });
    $(document).on('click', '[data-js-action="closeEditModal"]', closeEditModal);
    $(document).on('click', '[data-js-action="closeDetalleModal"]', closeDetalleModal);
    $formRegister.on('reset', function () {
      window.setTimeout(resetCategoryFilter, 0);
    });

    const ReglasProducto = {
      nombre: {
        regex: /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s.,#\/-]{3,25}$/u,
        errorMsg: 'El nombre del producto debe contener entre 3 y 50 caracteres alfanuméricos válidos.'
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

    /**
     * Valida los campos del formulario de producto.
     * @param {jQuery} $form
     * @returns {boolean}
     */
    function validarFormularioProducto($form) {
      const nombre = $.trim($form.find('[name="nombre_producto"]').val() || '');
      const porcentaje = $.trim($form.find('[name="porcentaje_ganancia"]').val() || '');
      const stockMinimo = $.trim($form.find('[name="stock_minimo"]').val() || '');

      if (!ReglasProducto.nombre.regex.test(nombre)) {
        showAlert(ReglasProducto.nombre.errorMsg, 'warning', 'Nombre Inválido');
        $form.find('[name="nombre_producto"]').focus();
        return false;
      }

      if (porcentaje !== '' && !ReglasProducto.porcentaje.regex.test(porcentaje)) {
        showAlert(ReglasProducto.porcentaje.errorMsg, 'warning', 'Porcentaje Inválido');
        $form.find('[name="porcentaje_ganancia"]').focus();
        return false;
      }

      if (stockMinimo !== '' && !ReglasProducto.stock.regex.test(stockMinimo)) {
        showAlert(ReglasProducto.stock.errorMsg, 'warning', 'Stock Inválido');
        $form.find('[name="stock_minimo"]').focus();
        return false;
      }

      return true;
    }

    /**
     * Calcula el precio de venta sugerido basado en costo y porcentaje de ganancia.
     * @param {number|string} costo
     * @param {number|string} porcentaje
     * @returns {number}
     */
    function calculateProductPrice(costo, porcentaje) {
      const cost = parseFloat(costo) || 0;
      const gain = parseFloat(porcentaje) || 0;
      return cost * (1 + gain / 100);
    }

    function updatePricePreview($costoInput, $porcentajeInput, $previewElement) {
      const costo = parseFloat($costoInput.val()) || 0;
      const porcentaje = parseFloat($porcentajeInput.val()) || 0;
      const precio = calculateProductPrice(costo, porcentaje);
      $previewElement.text('$' + precio.toFixed(2));
    }

    $('#new_costo, #new_porcentaje').on('input change', function () {
      updatePricePreview($('#new_costo'), $('#new_porcentaje'), $('#new_precio_preview'));
    });

    $('#edit_costo, #edit_porcentaje').on('input change', function () {
      updatePricePreview($('#edit_costo'), $('#edit_porcentaje'), $('#edit_precio_preview'));
    });

    // Registro de producto
    $formRegister.on('submit', function (e) {
      e.preventDefault();
      const $form = $(this);

      if (!validarFormularioProducto($form)) return;

      const $btnSubmit = $form.find('button[type="submit"]');
      $btnSubmit.prop('disabled', true).text('Guardando...');

      const formData = new FormData(this);

      $.ajax({
        url: $form.attr('action') || '?url=producto&type=register',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (resp) {
          if (resp && resp.status === 'success') {
            showAlert(resp.message || 'Producto registrado correctamente.', 'success', 'Éxito');
            $modal.addClass('hidden');
            $form[0].reset();
            resetCategoryFilter();
            if ($.fn.DataTable.isDataTable($tablaProductos)) {
              $tablaProductos.DataTable().ajax.reload(null, false);
            }
          } else {
            showAlert((resp && resp.message) || 'No se pudo registrar el producto.', 'error', 'Error');
          }
        },
        error: function (xhr, status, error) {
          console.error('[Error AJAX Producto Register]', error);
          showAlert('Ocurrió un error al comunicarse con el servidor.', 'error', 'Error de Red');
        },
        complete: function () {
          $btnSubmit.prop('disabled', false).text('Guardar Producto');
        }
      });
    });

    // Actualización de producto
    $formEdit.on('submit', function (e) {
      e.preventDefault();
      const $form = $(this);

      if (!validarFormularioProducto($form)) return;

      const $btnSubmit = $form.find('button[type="submit"]');
      $btnSubmit.prop('disabled', true).text('Actualizando...');

      const formData = new FormData(this);

      $.ajax({
        url: $form.attr('action') || '?url=producto&type=update',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (resp) {
          if (resp && resp.status === 'success') {
            showAlert(resp.message || 'Producto actualizado correctamente.', 'success', 'Actualizado');
            $modalEdit.addClass('hidden');
            if ($.fn.DataTable.isDataTable($tablaProductos)) {
              $tablaProductos.DataTable().ajax.reload(null, false);
            }
          } else {
            showAlert((resp && resp.message) || 'No se pudo actualizar el producto.', 'error', 'Error');
          }
        },
        error: function (xhr, status, error) {
          console.error('[Error AJAX Producto Update]', error);
          showAlert('Ocurrió un error al actualizar el producto.', 'error', 'Error de Red');
        },
        complete: function () {
          $btnSubmit.prop('disabled', false).text('Actualizar Producto');
        }
      });
    });

    // Vista de detalles
    function viewDetails(data) {
      currentRecord = data;

      $('#det_nombre').text(data.nombre_producto);
      $('#det_codigo').text('#' + String(data.codigo_producto).padStart(4, '0'));
      $('#det_estado').text(data.estado == 1 ? 'Activo' : 'Inactivo');
      $('#det_categoria').text(data.nombre_categoria || '—');
      $('#det_porcentaje').text(parseFloat(data.porcentaje_ganancia || 0).toFixed(2) + '%');
      $('#det_costo_compra').text('$' + parseFloat(data.costo_compra || data.costo || 0).toFixed(2));
      $('#det_precio').text('$' + parseFloat(data.precio || 0).toFixed(2));
      $('#det_stock').text(data.stock_actual + ' / ' + data.stock_minimo);
      $('#det_descripcion').text(data.descripcion || '—');

      $('#btnDetalleEditar').off('click').on('click', function () {
        closeDetalleModal();
        openEditModal(currentRecord);
      });

      $('#btnDetalleEliminar').off('click').on('click', function () {
        confirmDelete(currentRecord.codigo_producto);
      });

      $modalDetalle.removeClass('hidden');
    }

    function openEditModal(data) {
      $('#edit_id').val(data.codigo_producto);
      $('#edit_nombre').val(data.nombre_producto);
      $('#edit_categoria').val(data.codigo_categoria);

      const costoCompra = parseFloat(data.costo_compra || data.costo || 0).toFixed(2);
      $('#edit_costo').val(costoCompra);
      $('#edit_porcentaje').val(parseFloat(data.porcentaje_ganancia || 0).toFixed(2));
      $('#edit_precio_preview').text('$' + calculateProductPrice(costoCompra, data.porcentaje_ganancia || 0).toFixed(2));
      $('#edit_descripcion').val(data.descripcion || '');
      $('#edit_stock_actual').val(data.stock_actual);
      $('#edit_stock_minimo').val(data.stock_minimo);
      $('#edit_estado').prop('checked', data.estado == 1);

      $modalEdit.removeClass('hidden');
    }

    function confirmDelete(id) {
      showConfirm('¿Desea desactivar este producto del inventario?', 'Desactivar producto', 'Sí, desactivar')
        .then(function (confirmed) {
          if (!confirmed) return;

          $.ajax({
            url: '?url=producto&type=main',
            type: 'POST',
            data: { deleteProduct: 'true', idproducto: id },
            dataType: 'json',
            success: function (resp) {
              if (resp && resp.status === 'success') {
                showAlert(resp.message || 'Producto desactivado correctamente.', 'success');
                closeDetalleModal();
                if ($.fn.DataTable.isDataTable($tablaProductos)) {
                  $tablaProductos.DataTable().ajax.reload(null, false);
                }
              } else {
                showAlert((resp && resp.message) || 'No se pudo desactivar el producto.', 'error');
              }
            },
            error: function () {
              showAlert('No se pudo comunicar con el servidor para desactivar el producto.', 'error', 'Error de conexión');
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

    // Configuración DataTables Server-Side
    window.CopiGabyDataTablesConfig = window.CopiGabyDataTablesConfig || {};
    window.CopiGabyDataTablesConfig.productos = {
      serverSide: true,
      processing: true,
      searchDelay: 350,
      ajax: {
        url: '?url=producto&type=list',
        type: 'POST',
        dataType: 'json'
      },
      pageLength: 10,
      lengthMenu: [[10, 25, 50], [10, 25, 50]],
      columns: [
        {
          data: 'nombre_producto',
          render: function (data, type, row) {
            if (type !== 'display') return data;
            return '<div class="font-black text-navy-dark uppercase">' + escapeTableText(data) + '</div>'
              + '<div class="text-[0.7rem] text-gray-400 font-bold">COD: #'
              + escapeTableText(String(row.codigo_producto).padStart(4, '0')) + '</div>';
          }
        },
        {
          data: 'nombre_categoria',
          render: function (data, type, row) {
            if (type !== 'display') return data;
            return '<div class="text-navy-light">' + escapeTableText(data || '—') + '</div>'
              + '<span class="inline-block mt-1 px-2 py-0.5 rounded bg-orange/10 text-orange-dk text-[0.65rem] font-black uppercase">'
              + escapeTableText(row.porcentaje_ganancia || 0) + '% Ganancia</span>';
          }
        },
        {
          data: 'stock_actual',
          render: function (data, type, row) {
            if (type !== 'display') return data;
            const stockActual = Number(data) || 0;
            const stockMinimo = Number(row.stock_minimo) || 0;
            const stockClass = stockActual <= stockMinimo ? 'text-red-500 font-black' : 'text-green-600';
            return '<span class="' + stockClass + '">' + escapeTableText(data) + '</span>'
              + '<span class="text-gray-300 mx-1">/</span>'
              + '<span class="text-gray-400 text-[0.75rem] font-bold">'
              + escapeTableText(row.stock_minimo) + '</span>';
          }
        },
        {
          data: 'precio',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '$' + (Number(data) || 0).toLocaleString('en-US', {
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
            });
          }
        },
        {
          data: 'estado',
          render: function (data, type) {
            if (type !== 'display') return data;
            const activo = Number(data) === 1;
            return '<span class="px-3 py-1 rounded-full text-[0.65rem] font-black uppercase '
              + (activo ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') + '">'
              + (activo ? 'Activo' : 'Inactivo') + '</span>';
          }
        },
        {
          data: 'codigo_producto',
          orderable: false,
          searchable: false,
          render: function () {
            return '<button type="button" class="js-view-product-details group relative text-orange-dk p-2 hover:bg-orange/10 rounded-lg transition-colors">'
              + '<span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Consultar</span>'
              + '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'
              + '</button>';
          }
        }
      ],
      initComplete: function () {
        const api = this.api();
        $(api.table().body()).on('click', '.js-view-product-details', function () {
          const producto = api.row($(this).closest('tr')).data();
          if (producto) viewDetails(producto);
        });
      }
    };

    window.toggleModal = toggleModal;
    window.resetCategoryFilter = resetCategoryFilter;
    window.viewDetails = viewDetails;
    window.closeDetalleModal = closeDetalleModal;
    window.calculateProductPrice = calculateProductPrice;
    window.updatePricePreview = updatePricePreview;
    window.openEditModal = openEditModal;
    window.closeEditModal = closeEditModal;
    window.confirmDelete = confirmDelete;
    window.escapeTableText = escapeTableText;
  });
})(window, document, window.jQuery);

