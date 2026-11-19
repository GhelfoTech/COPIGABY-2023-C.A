(function (window, document, $) {
  'use strict';

  $(function () {
    const modal = document.getElementById('modalMoneda');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => modal.classList.toggle('hidden');
    btnOpen.onclick = toggleModal;

    const monedaActivaId = Number($('#moduloMoneda').data('monedaActivaId'));
    let currentRecord = null;

    function viewDetails(id) {
      CopiGabyAjax.request('?url=moneda&type=details', {
        method: 'POST',
        data: { id: id }
      })
    .then(r => r.json())
    .then(data => {
      if (!data || data.status === 'error') {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'No se pudo cargar la moneda.' });
        return;
      }
      currentRecord = data;
      document.getElementById('det_codigo').textContent = '#' + String(data.codigo_moneda).padStart(3, '0');
      document.getElementById('det_nombre').textContent = data.nombre_moneda;
      document.getElementById('det_simbolo').textContent = data.simbolo;
      document.getElementById('det_tasa').textContent = parseFloat(data.tasa_cambio).toFixed(2);
      document.getElementById('det_estado').textContent = parseInt(data.estado, 10) === 1 ? 'Habilitada' : 'Inactiva';
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => eliminar(currentRecord.codigo_moneda);
      const esGlobal = parseInt(data.codigo_moneda, 10) === monedaActivaId;
      document.getElementById('btnDetalleEliminar').classList.toggle('hidden', esGlobal);
      document.getElementById('modalDetalle').classList.remove('hidden');
    })
    .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación.' }));
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(data) {
      document.getElementById('edit_id').value = data.codigo_moneda;
      document.getElementById('edit_nombre').value = data.nombre_moneda;
      document.getElementById('edit_simbolo').value = data.simbolo;
      document.getElementById('edit_tasa').value = data.tasa_cambio;
      document.getElementById('edit_estado').checked = (parseInt(data.estado, 10) === 1);
      document.getElementById('modalEditMoneda').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('modalEditMoneda').classList.add('hidden');
    }

    function activarMoneda(id, nombre) {
      Swal.fire({
    title: '¿Establecer moneda global?',
    text: `"${nombre}" se aplicará en Pedidos, Compras y conversiones del sistema.`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Sí, activar',
    cancelButtonText: 'Cancelar',
      }).then(result => {
    if (!result.isConfirmed) return;
    const f = new FormData();
    f.append('idmoneda', String(id));
    CopiGabyAjax.request('?url=moneda&type=setActive', { method: 'POST', body: f })
      .then(r => r.json())
      .then(d => {
        if (d.status === 'success') {
          Swal.fire({ icon: 'success', title: 'Moneda activa', text: d.message, timer: 1500, showConfirmButton: false })
            .then(() => CopiGabyAjax.refresh());
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'No se pudo activar la moneda.' });
        }
      })
      .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación.' }));
      });
    }

    function eliminar(id) {
      Swal.fire({
        title: '¿Desactivar moneda?',
        text: 'La moneda quedará inactiva en el catálogo.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar',
      }).then(result => {
        if (!result.isConfirmed) return;
        const f = new FormData();
        f.append('deleteMoneda', 'true');
        f.append('idmoneda', String(id));
        CopiGabyAjax.request('?url=moneda&type=main', { method: 'POST', body: f })
          .then(r => r.json())
          .then(d => {
            if (d.status === 'success') {
              Swal.fire({ icon: 'success', title: 'Desactivada', timer: 1200, showConfirmButton: false })
                .then(() => CopiGabyAjax.refresh());
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: d.message || 'No se pudo procesar.' });
            }
          })
          .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación.' }));
      });
    }

    if (typeof toggleModal === "function") window.toggleModal = toggleModal;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof openEditModal === "function") window.openEditModal = openEditModal;
    if (typeof closeEditModal === "function") window.closeEditModal = closeEditModal;
    if (typeof activarMoneda === "function") window.activarMoneda = activarMoneda;
    if (typeof eliminar === "function") window.eliminar = eliminar;
  });
})(window, document, window.jQuery);
