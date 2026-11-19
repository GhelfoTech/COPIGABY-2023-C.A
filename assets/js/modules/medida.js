(function (window, document, $) {
  'use strict';

  $(function () {
    window.CopiGabyShowModuleFlash();

    const modal = document.getElementById('modalMedida');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => modal.classList.toggle('hidden');
    btnOpen.onclick = toggleModal;

    let currentRecord = null;

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_codigo').textContent = '#' + String(data.codigo_media).padStart(3, '0');
      document.getElementById('det_nombre').textContent = data.nombre;
      document.getElementById('det_abreviatura').textContent = data.abreviatura || '—';
      document.getElementById('det_cantidad_unidad').textContent = (data.cantidad_unidad ?? 1);
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(currentRecord.codigo_media);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(data) {
    document.getElementById('edit_codigo').value = data.codigo_media;
    document.getElementById('edit_nombre').value = data.nombre;
    document.getElementById('edit_abreviatura').value = data.abreviatura || '';
    document.getElementById('edit_cantidad_unidad').value = data.cantidad_unidad ?? 1;
    document.getElementById('edit_estado').checked = (data.estado == 1);
    document.getElementById('modalEditMedida').classList.remove('hidden');
    }

    function closeEditModal() {
    document.getElementById('modalEditMedida').classList.add('hidden');
    }

    async function confirmDelete(id) {
      if(await showConfirm('¿Desea desactivar esta unidad de medida?', 'Desactivar unidad de medida', 'Sí, desactivar')) {
    const f = new FormData();
    f.append('deleteMedida', 'true');
    f.append('codigo_media', id);
    CopiGabyAjax.request('?url=medida&type=main', { method:'POST', body:f })
    .then(r => r.json())
    .then(d => showMutationResultAndReload(d, 'Unidad desactivada correctamente.', 'No se pudo desactivar la unidad.'))
    .catch(() => showAlert('No se pudo comunicar con el servidor para desactivar la unidad.', 'error', 'Error de conexión'));
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
