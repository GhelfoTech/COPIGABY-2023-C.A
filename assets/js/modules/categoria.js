(function (window, document, $) {
  'use strict';

  $(function () {
    const modal = document.getElementById('modalCategory');
    const btnOpen = document.getElementById('btnOpenModal');
    const closeBtns = document.querySelectorAll('.closeModal');
    const overlay = document.getElementById('overlay');
    const toggleModal = () => modal.classList.toggle('hidden');

    btnOpen.onclick = toggleModal;
    closeBtns.forEach(btn => btn.onclick = toggleModal);
    overlay.onclick = toggleModal;

    let currentRecord = null;

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_codigo').textContent = '#' + String(data.codigo_categoria).padStart(3, '0');
      document.getElementById('det_nombre').textContent = data.nombre_categoria;
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      document.getElementById('btnDetalleEditar').onclick = () => {
    closeDetalleModal();
    openEditModal(data.codigo_categoria, data.nombre_categoria, data.estado);
      };
      document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(data.codigo_categoria);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(id, nombre, estado) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_nombre').value = nombre;
      document.getElementById('edit_estado').checked = (estado == 1);
      document.getElementById('modalEditCategory').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('modalEditCategory').classList.add('hidden');
    }

    async function confirmDelete(id) {
      if(await showConfirm('¿Desea desactivar esta categoría?', 'Desactivar categoría', 'Sí, desactivar')) {
    const f = new FormData();
    f.append('deleteCategory', 'true');
    f.append('idcategoria', id);
    CopiGabyAjax.request('?url=categoria&type=main', { method:'POST', body:f })
    .then(r => r.json())
    .then(d => showMutationResultAndReload(d, 'Categoría desactivada correctamente.', 'No se pudo desactivar la categoría.'))
    .catch(() => showAlert('No se pudo comunicar con el servidor para desactivar la categoría.', 'error', 'Error de conexión'));
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
