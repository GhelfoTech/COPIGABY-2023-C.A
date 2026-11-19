(function (window, document, $) {
  'use strict';

  $(function () {
    const modalClienteRegistro = document.getElementById('modalClienteRegistro');
    const formClienteRegistro = document.getElementById('formClienteRegistro');
    const btnOpen = document.getElementById('btnOpenModal');

    function abrirModalClienteRegistro() {
      modalClienteRegistro.classList.remove('hidden');
      modalClienteRegistro.setAttribute('aria-hidden', 'false');
      document.getElementById('clienteRegistroCedula').focus();
    }

    function cerrarModalClienteRegistro() {
      modalClienteRegistro.classList.add('hidden');
      modalClienteRegistro.setAttribute('aria-hidden', 'true');
    }

    btnOpen.addEventListener('click', abrirModalClienteRegistro);
    modalClienteRegistro.addEventListener('click', (event) => {
      if (event.target.closest('[data-close-cliente-modal]')) {
    cerrarModalClienteRegistro();
      }
    });

    let currentRecord = null;

    function viewDetails(data) {
      currentRecord = data;
      document.getElementById('det_cedula').textContent = data.cedula_cliente;
      document.getElementById('det_nombre').textContent = data.nombre;
      document.getElementById('det_telefono').textContent = data.telefono || '—';
      document.getElementById('det_correo').textContent = data.correo || '—';
      document.getElementById('det_direccion').textContent = data.direccion || '—';
      document.getElementById('det_estado').textContent = data.estado == 1 ? 'Activo' : 'Inactivo';
      document.getElementById('btnDetalleEditar').onclick = () => { closeDetalleModal(); openEditModal(currentRecord); };
      document.getElementById('btnDetalleEliminar').onclick = () => confirmDelete(currentRecord.cedula_cliente);
      document.getElementById('modalDetalle').classList.remove('hidden');
    }

    function closeDetalleModal() {
      document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(data) {
        document.getElementById('edit_id_actual').value = data.cedula_cliente;
        document.getElementById('edit_cedula').value = data.cedula_cliente;
        document.getElementById('edit_nombre').value = data.nombre;
        window.setTelefonoFieldValue('edit_telefono', data.telefono);
        document.getElementById('edit_correo').value = data.correo;
        document.getElementById('edit_direccion').value = data.direccion;
        document.getElementById('edit_estado').checked = data.estado == 1;
        document.getElementById('modalEditCliente').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEditCliente').classList.add('hidden');
    }

    async function confirmDelete(id) {
      if(await showConfirm('¿Desea desactivar este cliente?', 'Desactivar cliente', 'Sí, desactivar')) {
        const f = new FormData();
        f.append('deleteCliente', 'true');
        f.append('idcliente', id);
        CopiGabyAjax.request('?url=cliente&type=main', { method:'POST', body:f })
        .then(r => r.json())
        .then(d => showMutationResultAndReload(d, 'Cliente desactivado correctamente.', 'No se pudo desactivar el cliente.'))
        .catch(() => showAlert('No se pudo comunicar con el servidor para desactivar el cliente.', 'error', 'Error de conexión'));
      }
    }

    if (typeof abrirModalClienteRegistro === "function") window.abrirModalClienteRegistro = abrirModalClienteRegistro;
    if (typeof cerrarModalClienteRegistro === "function") window.cerrarModalClienteRegistro = cerrarModalClienteRegistro;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof openEditModal === "function") window.openEditModal = openEditModal;
    if (typeof closeEditModal === "function") window.closeEditModal = closeEditModal;
    if (typeof confirmDelete === "function") window.confirmDelete = confirmDelete;
  });
})(window, document, window.jQuery);
