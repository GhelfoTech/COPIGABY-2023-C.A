(function (window, document, $) {
  'use strict';

  $(function () {
    const modal = document.getElementById('modalProduct');
    const btnOpen = document.getElementById('btnOpenModal');
    const closeBtns = document.querySelectorAll('.closeModal');
    const overlay = document.getElementById('overlay');
    const toggleModal = () => modal.classList.toggle('hidden');

    btnOpen.onclick = toggleModal;
    closeBtns.forEach(btn => btn.onclick = toggleModal);
    overlay.onclick = toggleModal;

    function resetCategoryFilter() {
      window.resetSearchableSelect(document.getElementById('selectCategoriaProducto'));
    }

    window.attachSearchableSelect(
      document.getElementById('buscarCategoriaProducto'),
      document.getElementById('selectCategoriaProducto')
    );
    document.getElementById('formProductoRegister').addEventListener('reset', () => window.setTimeout(resetCategoryFilter, 0));
    btnOpen.addEventListener('click', resetCategoryFilter);

    const newCostoInput = document.getElementById('new_costo');
    const newPorcentajeInput = document.getElementById('new_porcentaje');
    if (newCostoInput && newPorcentajeInput) {
      const updateNewPrecio = () => updatePricePreview(newCostoInput, newPorcentajeInput, 'new_precio_preview');
      newCostoInput.addEventListener('input', updateNewPrecio);
      newPorcentajeInput.addEventListener('input', updateNewPrecio);
    }

    const editCostoInput = document.getElementById('edit_costo');
    const editPorcentajeInput = document.getElementById('edit_porcentaje');
    if (editCostoInput && editPorcentajeInput) {
      const updateEditPrecio = () => updatePricePreview(editCostoInput, editPorcentajeInput, 'edit_precio_preview');
      editCostoInput.addEventListener('input', updateEditPrecio);
      editPorcentajeInput.addEventListener('input', updateEditPrecio);
    }

    let currentRecord = null;

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_nombre').textContent = data.nombre_producto;
      document.getElementById('det_codigo').textContent = '#' + String(data.codigo_producto).padStart(4, '0');
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      document.getElementById('det_categoria').textContent = data.nombre_categoria || '—';
      document.getElementById('det_porcentaje').textContent = parseFloat(data.porcentaje_ganancia || data.porcentaje_ganancia || 0).toFixed(2) + '%';
      document.getElementById('det_costo_compra').textContent = '$' + parseFloat(data.costo_compra || data.costo || 0).toFixed(2);
      document.getElementById('det_precio').textContent = '$' + parseFloat(data.precio || 0).toFixed(2);
      document.getElementById('det_stock').textContent = data.stock_actual + ' / ' + data.stock_minimo;
      document.getElementById('det_descripcion').textContent = data.descripcion || '—';
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(currentRecord.codigo_producto);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function calculateProductPrice(costo, porcentaje) {
      const cost = parseFloat(costo) || 0;
      const gain = parseFloat(porcentaje) || 0;
      return cost * (1 + gain / 100);
    }

    function updatePricePreview(costoInput, porcentajeInput, previewId) {
      const costo = parseFloat(costoInput.value) || 0;
      const porcentaje = parseFloat(porcentajeInput.value) || 0;
      const precio = calculateProductPrice(costo, porcentaje);
      document.getElementById(previewId).textContent = '$' + precio.toFixed(2);
    }

    function openEditModal(data) {
    document.getElementById('edit_id').value = data.codigo_producto;
    document.getElementById('edit_nombre').value = data.nombre_producto;
    document.getElementById('edit_categoria').value = data.codigo_categoria;
    const costoCompra = parseFloat(data.costo_compra || data.costo || 0).toFixed(2);
    document.getElementById('edit_costo').value = costoCompra;
    document.getElementById('edit_porcentaje').value = parseFloat(data.porcentaje_ganancia || data.porcentaje_ganancia || 0).toFixed(2);
    document.getElementById('edit_precio_preview').textContent = '$' + calculateProductPrice(costoCompra, data.porcentaje_ganancia || data.porcentaje_ganancia || 0).toFixed(2);
    document.getElementById('edit_descripcion').value = data.descripcion;
    document.getElementById('edit_stock_actual').value = data.stock_actual;
    document.getElementById('edit_stock_minimo').value = data.stock_minimo;
    document.getElementById('edit_estado').checked = (data.estado == 1);
    document.getElementById('modalEditProduct').classList.remove('hidden');
    }

    function closeEditModal() {
    document.getElementById('modalEditProduct').classList.add('hidden');
    }

    async function confirmDelete(id) {
      if(await showConfirm('¿Desea desactivar este producto del inventario?', 'Desactivar producto', 'Sí, desactivar')) {
    const f = new FormData();
    f.append('deleteProduct', 'true');
    f.append('idproducto', id);
    CopiGabyAjax.request('?url=producto&type=main', { method:'POST', body:f })
    .then(r => r.json())
    .then(d => showMutationResultAndReload(d, 'Producto desactivado correctamente.', 'No se pudo desactivar el producto.'))
    .catch(() => showAlert('No se pudo comunicar con el servidor para desactivar el producto.', 'error', 'Error de conexión'));
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
        window.jQuery(api.table().body()).on('click', '.js-view-product-details', function () {
          const producto = api.row(window.jQuery(this).closest('tr')).data();
          if (producto) viewDetails(producto);
        });
      }
    };

    if (typeof toggleModal === "function") window.toggleModal = toggleModal;
    if (typeof resetCategoryFilter === "function") window.resetCategoryFilter = resetCategoryFilter;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof calculateProductPrice === "function") window.calculateProductPrice = calculateProductPrice;
    if (typeof updatePricePreview === "function") window.updatePricePreview = updatePricePreview;
    if (typeof openEditModal === "function") window.openEditModal = openEditModal;
    if (typeof closeEditModal === "function") window.closeEditModal = closeEditModal;
    if (typeof confirmDelete === "function") window.confirmDelete = confirmDelete;
    if (typeof escapeTableText === "function") window.escapeTableText = escapeTableText;
  });
})(window, document, window.jQuery);
