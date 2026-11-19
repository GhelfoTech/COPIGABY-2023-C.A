(function (window, document, $) {
  'use strict';

  $(function () {
    const toggleModal = (id) => document.getElementById(id).classList.toggle('hidden');

    let currentRecord = null;
    let materialesRegister = [];
    let materialesEdit = [];

    function attachMaterialFilter(mode) {
      const input = document.getElementById('buscarProductoServicio_' + mode);
      const select = document.getElementById('selProducto_' + mode);
      window.attachSearchableSelect(input, select);
      return () => window.resetSearchableSelect(select);
    }

    const resetMaterialFilters = {
      register: attachMaterialFilter('register'),
      edit: attachMaterialFilter('edit')
    };

    function abrirModalRegistro() {
      materialesRegister = [];
      renderMateriales('register');
      resetMaterialFilters.register();
      toggleModal('modalRegister');
    }

    function agregarMaterial(mode) {
      const sel = document.getElementById('selProducto_' + mode);
      const cant = document.getElementById('cantProducto_' + mode);
      const codigo = sel.value;
      if (!codigo) { showAlert('Seleccione un producto o insumo.', 'warning', 'Material requerido'); return; }

      const nombre = sel.options[sel.selectedIndex].dataset.nombre;
      const stockDisp = parseInt(sel.options[sel.selectedIndex].dataset.stock || '0', 10);
      const cantidadRaw = Number(cant.value);
      if (!Number.isInteger(cantidadRaw) || cantidadRaw < 1) {
    showAlert('La cantidad debe ser un número entero mayor a cero.', 'warning', 'Cantidad no válida');
    return;
      }
      const cantidad = cantidadRaw;

      const arr = mode === 'register' ? materialesRegister : materialesEdit;
      const existente = arr.find(m => String(m.codigo_producto) === String(codigo));
      if (existente) {
    if (!Number.isInteger(Number(existente.cantidad_usada)) || Number(existente.cantidad_usada) < 1) {
      showAlert('Corrija la cantidad fraccionaria del material antes de agregar más unidades.', 'warning', 'Cantidad no válida');
      return;
    }
    existente.cantidad_usada = Number(existente.cantidad_usada) + cantidad;
      } else {
    arr.push({ codigo_producto: codigo, nombre: nombre, cantidad_usada: cantidad, stock: stockDisp });
      }

      renderMateriales(mode);
      window.resetSearchableSelect(sel);
      cant.value = 1;
    }

    async function quitarMaterial(mode, codigo) {
      const arr = mode === 'register' ? materialesRegister : materialesEdit;
      const material = arr.find(m => String(m.codigo_producto) === String(codigo));
      if (!material) return;

      const confirmado = await showConfirm(
    '¿Desea quitar "' + material.nombre + '" de los materiales del servicio?',
    'Quitar material',
    'Sí, quitar'
      );
      if (!confirmado) return;

      if (mode === 'register') {
    materialesRegister = materialesRegister.filter(m => String(m.codigo_producto) !== String(codigo));
      } else {
    materialesEdit = materialesEdit.filter(m => String(m.codigo_producto) !== String(codigo));
      }
      renderMateriales(mode);
    }

    function actualizarCantidadMaterial(mode, codigo, input) {
      const cantidad = Number(input.value);
      if (!Number.isInteger(cantidad) || cantidad < 1) {
    input.setCustomValidity('La cantidad debe ser un número entero mayor a cero.');
    return;
      }

      input.setCustomValidity('');
      const arr = mode === 'register' ? materialesRegister : materialesEdit;
      const material = arr.find(m => String(m.codigo_producto) === String(codigo));
      if (!material) return;

      material.cantidad_usada = cantidad;
      document.getElementById('materiales_' + mode).value = JSON.stringify(
    arr.map(m => ({ codigo_producto: m.codigo_producto, cantidad_usada: m.cantidad_usada }))
      );
    }

    function renderMateriales(mode) {
      const arr = mode === 'register' ? materialesRegister : materialesEdit;
      const lista = document.getElementById('listaMateriales_' + mode);
      const hidden = document.getElementById('materiales_' + mode);
      lista.innerHTML = '';

      if (arr.length === 0) {
    lista.innerHTML = '<p class="col-span-full text-xs text-gray-400 italic">Sin materiales asignados. Puede guardar el servicio sin materiales.</p>';
      } else {
    arr.forEach(m => {
      const card = document.createElement('div');
      card.className = 'flex items-center justify-between gap-2 bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm';
      const info = document.createElement('div');
      info.className = 'min-w-0';
      const name = document.createElement('p');
      name.className = 'font-bold text-navy-dark text-sm truncate';
      name.textContent = m.nombre;
      const stock = document.createElement('p');
      stock.className = 'text-[0.65rem] text-gray-400';
      stock.textContent = 'Stock disponible: ' + (m.stock ?? '—');
      info.append(name, stock);

      const controls = document.createElement('div');
      controls.className = 'flex items-center gap-2 shrink-0';
      const quantityLabel = document.createElement('span');
      quantityLabel.className = 'text-[0.65rem] font-black text-gray-500';
      quantityLabel.textContent = 'Unid.';
      const quantityInput = document.createElement('input');
      quantityInput.type = 'number';
      quantityInput.min = '1';
      quantityInput.step = '1';
      quantityInput.value = String(m.cantidad_usada);
      quantityInput.className = 'w-20 rounded border px-2 py-1 text-center';
      quantityInput.setAttribute('aria-label', 'Unidades usadas de ' + m.nombre);
      quantityInput.addEventListener('input', () => actualizarCantidadMaterial(mode, m.codigo_producto, quantityInput));
      actualizarCantidadMaterial(mode, m.codigo_producto, quantityInput);

      const remove = document.createElement('button');
      remove.type = 'button';
      remove.className = 'w-7 h-7 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 transition-colors';
      remove.title = 'Quitar';
      remove.setAttribute('aria-label', 'Quitar ' + m.nombre);
      remove.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
      remove.addEventListener('click', () => quitarMaterial(mode, m.codigo_producto));
      controls.append(quantityLabel, quantityInput, remove);
      card.append(info, controls);
      lista.appendChild(card);
        });
      }

      hidden.value = JSON.stringify(
        arr.map(m => ({ codigo_producto: m.codigo_producto, cantidad_usada: m.cantidad_usada }))
      );
    }

    function cargarMaterialesServicio(codigo, callback) {
      const f = new FormData();
      f.append('getMaterials', 'true');
      f.append('idservicio', codigo);
      CopiGabyAjax.request('?url=servicio&type=main', { method: 'POST', body: f })
        .then(r => r.json())
        .then(d => { if (typeof callback === 'function') callback(d); })
        .catch(() => { if (typeof callback === 'function') callback([]); });
    }

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_codigo').textContent = '#' + String(data.codigo_servicio).padStart(3, '0');
      document.getElementById('det_nombre').textContent = data.nombre_servicio;
      document.getElementById('det_precio').textContent = '$' + parseFloat(data.precio).toFixed(2);
      document.getElementById('det_descripcion').textContent = data.descripcion || '—';
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      const cont = document.getElementById('det_materiales');
      cont.innerHTML = '<p class="text-xs text-gray-400 italic">Cargando…</p>';
      cargarMaterialesServicio(data.codigo_servicio, (mats) => {
        if (!mats || mats.length === 0) {
          cont.innerHTML = '<p class="text-xs text-gray-400 italic">Sin materiales asignados.</p>';
          return;
        }
        cont.className = 'grid grid-cols-1 sm:grid-cols-2 gap-2';
        cont.innerHTML = '';
        mats.forEach(m => {
          const card = document.createElement('div');
          card.className = 'flex items-center justify-between gap-2 bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm';
          card.innerHTML =
            '<p class="font-bold text-navy-dark text-sm truncate">' + (m.nombre_producto || ('Prod. #' + m.codigo_producto)) + '</p>' +
            '<span class="text-xs font-black text-orange-dk bg-orange/10 px-2 py-1 rounded-lg shrink-0">Usa: ' + (m.cantidad_usada ?? 0) + '</span>';
          cont.appendChild(card);
        });
      });
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => eliminar(currentRecord.codigo_servicio);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(data) {
      resetMaterialFilters.edit();
      document.getElementById('edit_id').value = data.codigo_servicio;
      document.getElementById('edit_nombre').value = data.nombre_servicio;
      document.getElementById('edit_precio').value = data.precio;
      document.getElementById('edit_descripcion').value = data.descripcion;
      document.getElementById('edit_estado').checked = data.estado == 1;
      materialesEdit = [];
      renderMateriales('edit');
      cargarMaterialesServicio(data.codigo_servicio, (mats) => {
        (mats || []).forEach(m => {
          materialesEdit.push({ codigo_producto: m.codigo_producto, nombre: m.nombre_producto || ('Prod. #' + m.codigo_producto), cantidad_usada: parseFloat(m.cantidad_usada) });
        });
        renderMateriales('edit');
      });
      toggleModal('modalEdit');
    }

    async function eliminar(id) {
      if(await showConfirm('¿Desea desactivar este servicio?', 'Desactivar servicio', 'Sí, desactivar')) {
        const f = new FormData();
        f.append('deleteServicio', 'true');
        f.append('idservicio', id);
        CopiGabyAjax.request('?url=servicio&type=main', { method:'POST', body:f })
        .then(r => r.json())
        .then(d => showMutationResultAndReload(d, 'Servicio desactivado correctamente.', 'No se pudo desactivar el servicio.'))
        .catch(() => showAlert('No se pudo comunicar con el servidor para desactivar el servicio.', 'error', 'Error de conexión'));
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
    window.CopiGabyDataTablesConfig.servicios = {
      serverSide: true,
      processing: true,
      searchDelay: 350,
      ajax: {
        url: '?url=servicio&type=list',
        type: 'POST',
        dataType: 'json'
      },
      pageLength: 10,
      lengthMenu: [[10, 25, 50], [10, 25, 50]],
      order: [[0, 'desc']],
      columns: [
        {
          data: 'codigo_servicio',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '#' + escapeTableText(String(data).padStart(3, '0'));
          }
        },
        {
          data: 'nombre_servicio',
          render: function (data, type) {
            if (type !== 'display') return data;
            return '<span class="uppercase font-bold text-navy-light">' + escapeTableText(data) + '</span>';
          }
        },
        {
          data: 'descripcion',
          render: function (data, type) {
            if (type !== 'display') return data;
            const descripcion = escapeTableText(data || '—');
            return '<span class="text-gray-500 italic text-xs truncate max-w-xs" title="'
              + descripcion + '">' + descripcion + '</span>';
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
          data: 'codigo_servicio',
          orderable: false,
          searchable: false,
          render: function () {
            return '<button type="button" class="js-view-service-details group relative text-orange-dk p-2 hover:bg-orange/10 rounded-lg transition-colors">'
              + '<span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-navy-dark text-white text-[10px] px-2 py-1 rounded whitespace-nowrap pointer-events-none z-10 shadow-lg">Consultar</span>'
              + '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'
              + '</button>';
          }
        }
      ],
      initComplete: function () {
        const api = this.api();
        window.jQuery(api.table().body()).on('click', '.js-view-service-details', function () {
          const servicio = api.row(window.jQuery(this).closest('tr')).data();
          if (servicio) viewDetails(servicio);
        });
      }
    };

    if (typeof toggleModal === "function") window.toggleModal = toggleModal;
    if (typeof attachMaterialFilter === "function") window.attachMaterialFilter = attachMaterialFilter;
    if (typeof abrirModalRegistro === "function") window.abrirModalRegistro = abrirModalRegistro;
    if (typeof agregarMaterial === "function") window.agregarMaterial = agregarMaterial;
    if (typeof quitarMaterial === "function") window.quitarMaterial = quitarMaterial;
    if (typeof actualizarCantidadMaterial === "function") window.actualizarCantidadMaterial = actualizarCantidadMaterial;
    if (typeof renderMateriales === "function") window.renderMateriales = renderMateriales;
    if (typeof cargarMaterialesServicio === "function") window.cargarMaterialesServicio = cargarMaterialesServicio;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof openEditModal === "function") window.openEditModal = openEditModal;
    if (typeof eliminar === "function") window.eliminar = eliminar;
  });
})(window, document, window.jQuery);
