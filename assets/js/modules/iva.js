(function (window, document, $) {
  'use strict';

  $(function () {
    window.CopiGabyShowModuleFlash();

    const modal = document.getElementById('modalIva');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => modal.classList.toggle('hidden');
    btnOpen.onclick = toggleModal;

    let currentRecord = null;

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_codigo').textContent = '#' + String(data.codigo_IVA).padStart(3, '0');
      document.getElementById('det_porcentaje').textContent = parseFloat(data.porcentaje_iva).toFixed(2) + '%';
      document.getElementById('det_fecha').textContent = data.fecha ? new Date(data.fecha).toLocaleDateString('es-VE') : '—';
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(currentRecord.codigo_IVA);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(data) {
    document.getElementById('edit_codigo').value = data.codigo_IVA;
    document.getElementById('edit_porcentaje').value = data.porcentaje_iva;
    document.getElementById('edit_estado').checked = (data.estado == 1);
    document.getElementById('modalEditIva').classList.remove('hidden');
    }

    function closeEditModal() {
    document.getElementById('modalEditIva').classList.add('hidden');
    }

    async function confirmDelete(id) {
      if(await showConfirm('¿Desea desactivar este registro de IVA?', 'Desactivar IVA', 'Sí, desactivar')) {
    const f = new FormData();
    f.append('deleteIva', 'true');
    f.append('codigo_IVA', id);
    CopiGabyAjax.request('?url=iva&type=main', { method:'POST', body:f })
    .then(r => r.json())
    .then(d => showMutationResultAndReload(d, 'Registro de IVA desactivado correctamente.', 'No se pudo desactivar el registro de IVA.'))
    .catch(() => showAlert('No se pudo comunicar con el servidor para desactivar el IVA.', 'error', 'Error de conexión'));
      }
    }

    if (typeof toggleModal === "function") window.toggleModal = toggleModal;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof openEditModal === "function") window.openEditModal = openEditModal;
    if (typeof closeEditModal === "function") window.closeEditModal = closeEditModal;
    if (typeof confirmDelete === "function") window.confirmDelete = confirmDelete;
  });
})(window, document, window.jQuery);
