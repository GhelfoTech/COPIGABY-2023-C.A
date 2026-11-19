(function (window, document, $) {
  'use strict';

  $(function () {
    window.CopiGabyShowModuleFlash();

    const toggleModal = (id) => document.getElementById(id).classList.toggle('hidden');

    let currentRecord = null;

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_codigo').textContent = '#' + String(data.codigo_metodo).padStart(3, '0');
      document.getElementById('det_nombre').textContent = data.nombre_metodo;
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => eliminar(currentRecord.codigo_metodo);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(data) {
      document.getElementById('edit_id').value = data.codigo_metodo;
      document.getElementById('edit_nombre').value = data.nombre_metodo;
      document.getElementById('edit_estado').checked = data.estado == 1;
      toggleModal('modalEdit');
    }

    async function eliminar(id) {
      if(await showConfirm('¿Desea desactivar este método de pago?', 'Desactivar método de pago', 'Sí, desactivar')) {
    const f = new FormData();
    f.append('deleteMetodo', 'true');
    f.append('codigo_metodo', id);
    CopiGabyAjax.request('?url=metodopago&type=main', { method:'POST', body:f })
    .then(r => r.json())
    .then(d => showMutationResultAndReload(d, 'Método de pago desactivado correctamente.', 'No se pudo desactivar el método de pago.'))
    .catch(() => showAlert('No se pudo comunicar con el servidor para desactivar el método de pago.', 'error', 'Error de conexión'));
      }
    }

    if (typeof toggleModal === "function") window.toggleModal = toggleModal;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof openEditModal === "function") window.openEditModal = openEditModal;
    if (typeof eliminar === "function") window.eliminar = eliminar;
  });
})(window, document, window.jQuery);
