(function (window, document, $) {
  'use strict';

  $(function () {
    function resetRoleFilter(mode) {
      const select = document.getElementById(mode === 'Register' ? 'selectRolRegister' : 'edit_rol');
      window.resetSearchableSelect(select);
    }

    function attachRoleFilter(mode) {
      const input = document.getElementById('buscarRol' + mode);
      const select = document.getElementById(mode === 'Register' ? 'selectRolRegister' : 'edit_rol');
      window.attachSearchableSelect(input, select);
    }

    attachRoleFilter('Register');
    attachRoleFilter('Edit');

    const toggleModal = (id) => {
      if (document.getElementById(id).classList.contains('hidden')) {
    if (id === 'modalRegister') resetRoleFilter('Register');
      }
      document.getElementById(id).classList.toggle('hidden');
    };

    let currentRecord = null;

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_cedula').textContent = 'V-' + data.cedula_usuario;
      document.getElementById('det_nombre').textContent = data.nombre_usuario;
      document.getElementById('det_telefono').textContent = data.telefono || '—';
      document.getElementById('det_rol').textContent = data.nombre_rol || '—';
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => eliminar(currentRecord.cedula_usuario);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(data) {
      document.getElementById('edit_id').value = data.cedula_usuario;
      document.getElementById('edit_cedula').value = data.cedula_usuario;
      document.getElementById('edit_nombre').value = data.nombre_usuario;
      window.setTelefonoFieldValue('edit_telefono', data.telefono);
      document.getElementById('edit_rol').value = data.codigo_rol;
      window.syncSearchableSelect(document.getElementById('edit_rol'));
      document.getElementById('edit_estado').checked = data.estado == 1;
      toggleModal('modalEdit');
    }
    async function eliminar(id) {
      if(await showConfirm('¿Desea desactivar este usuario?', 'Desactivar usuario', 'Sí, desactivar')) {
    const f = new FormData(); f.append('deleteUser', 'true'); f.append('idUser', id);
    CopiGabyAjax.request('?url=user&type=main', { method:'POST', body:f })
      .then(r => r.json())
      .then(d => showMutationResultAndReload(d, 'Usuario desactivado correctamente.', 'No se pudo desactivar el usuario.'))
      .catch(() => showAlert('No se pudo comunicar con el servidor para desactivar el usuario.', 'error', 'Error de conexión'));
      }
    }

    if (typeof resetRoleFilter === "function") window.resetRoleFilter = resetRoleFilter;
    if (typeof attachRoleFilter === "function") window.attachRoleFilter = attachRoleFilter;
    if (typeof toggleModal === "function") window.toggleModal = toggleModal;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof openEditModal === "function") window.openEditModal = openEditModal;
    if (typeof eliminar === "function") window.eliminar = eliminar;
  });
})(window, document, window.jQuery);
