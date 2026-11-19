(function (window, document, $) {
  'use strict';

  $(function () {
    const modal = document.getElementById('modalProveedor');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => modal.classList.toggle('hidden');
    btnOpen.onclick = toggleModal;

    let currentRecord = null;

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_razon').textContent = data.razon_social;
      document.getElementById('det_rif').textContent = data.rif_proveedor;
      document.getElementById('det_telefono').textContent = data.telefono || '—';
      document.getElementById('det_correo').textContent = data.correo || '—';
      document.getElementById('det_direccion').textContent = data.direccion || '—';
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(currentRecord.codigo_proveedor);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(data) {
    document.getElementById('edit_codigo').value = data.codigo_proveedor;
    document.getElementById('edit_razon').value = data.razon_social;
    document.getElementById('edit_rif').value = data.rif_proveedor;
    window.setTelefonoFieldValue('edit_telefono', data.telefono);
    document.getElementById('edit_correo').value = data.correo;
    document.getElementById('edit_direccion').value = data.direccion;
    document.getElementById('edit_estado').checked = (data.estado == 1);
    document.getElementById('modalEditProveedor').classList.remove('hidden');
    }

    function closeEditModal() {
    document.getElementById('modalEditProveedor').classList.add('hidden');
    }

    async function confirmDelete(id) {
      if(await showConfirm('¿Desea desactivar este proveedor?', 'Desactivar proveedor', 'Sí, desactivar')) {
    const f = new FormData();
    f.append('deleteProveedor', 'true');
    f.append('idproveedor', id);
    CopiGabyAjax.request('?url=proveedor&type=main', { method:'POST', body:f })
    .then(r => r.json())
    .then(d => showMutationResultAndReload(d, 'Proveedor desactivado correctamente.', 'No se pudo desactivar el proveedor.'))
    .catch(() => showAlert('No se pudo comunicar con el servidor para desactivar el proveedor.', 'error', 'Error de conexión'));
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
