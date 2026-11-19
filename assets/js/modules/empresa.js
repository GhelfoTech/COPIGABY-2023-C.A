(function (window, document, $) {
  'use strict';

  $(function () {
    const modal = document.getElementById('modalEmpresa');
    const btnOpen = document.getElementById('btnOpenModal');
    const toggleModal = () => modal.classList.toggle('hidden');
    btnOpen.onclick = toggleModal;

    let currentEmpresa = null;

    function closeDetalleModal() {
    document.getElementById('modalDetalle').classList.add('hidden');
    }

    function openEditModal(data) {
    currentEmpresa = data;
    document.getElementById('edit_codigo').value = data.codigo_empresa;
    document.getElementById('edit_nombre').value = data.nombre_empresa;
    const rif = String(data.rif_empresa || '').trim();
    const rifTipo = (rif.startsWith('V-') || rif.startsWith('J-')) ? rif.slice(0, 2) : 'J-';
    const rifNumero = (rif.startsWith('V-') || rif.startsWith('J-')) ? rif.slice(2).replace(/[^0-9]/g, '') : rif.replace(/[^0-9]/g, '');
    const rifTipoSelect = document.getElementById('edit_rif_tipo');
    if (rifTipoSelect) rifTipoSelect.value = rifTipo;
    document.getElementById('edit_rif').value = rifNumero;
    window.setTelefonoFieldValue('edit_telefono', data.telefono);
    document.getElementById('edit_correo').value = data.correo;
    document.getElementById('edit_direccion').value = data.direccion;
    document.getElementById('edit_logo').value = data.logo;
    document.getElementById('modalEditEmpresa').classList.remove('hidden');
    }

    function viewDetails(id) {
      CopiGabyAjax.request('?url=empresa&type=details', {
        method: 'POST',
        data: { id: id }
      })
      .then(r => r.json())
      .then(data => {
      currentEmpresa = data;
      document.getElementById('det_nombre').innerText = data.nombre_empresa;
      document.getElementById('det_rif').innerText = data.rif_empresa;
      document.getElementById('det_telefono').innerText = data.telefono;
      document.getElementById('det_correo').innerText = data.correo;
      document.getElementById('det_direccion').innerText = data.direccion;
      document.getElementById('modalDetalle').classList.remove('hidden');
      });
    }

    document.getElementById('btnDetalleEditar').addEventListener('click', function() {
        if (!currentEmpresa) return;
        closeDetalleModal();
        openEditModal(currentEmpresa);
    });

    document.getElementById('btnDetalleEliminar').addEventListener('click', function() {
        if (!currentEmpresa) return;
        confirmDelete(currentEmpresa.codigo_empresa);
    });

    function handleEditFromDetail() {
        if(!currentEmpresa) return;
        closeDetalleModal();
        openEditModal(currentEmpresa);
    }

    function handleDeleteFromDetail() {
        if(!currentEmpresa) return;
        confirmDelete(currentEmpresa.codigo_empresa);
    }

    function closeEditModal() {
        document.getElementById('modalEditEmpresa').classList.add('hidden');
    }

    async function confirmDelete(id) {
      if(await showConfirm('¿Desea eliminar definitivamente esta empresa? Esta acción no se puede deshacer.', 'Eliminar empresa', 'Sí, eliminar')) {
        const f = new FormData();
        f.append('deleteEmpresa', 'true');
        f.append('codigo_empresa', id);
        CopiGabyAjax.request('?url=empresa&type=main', { method:'POST', body:f })
        .then(r => r.json())
        .then(d => showMutationResultAndReload(d, 'Empresa eliminada correctamente.', 'No se pudo eliminar la empresa.'))
        .catch(() => showAlert('No se pudo comunicar con el servidor para eliminar la empresa.', 'error', 'Error de conexión'));
      }
    }

    function setRifForEmpresa(form) {
        const rifTipo = (form.querySelector('select[name="rif_tipo"]') || {}).value || 'J-';
        const rifNumero = (form.querySelector('input[name="rif_numero"]') || {}).value || '';
        const rifInput = form.querySelector('input[name="rif_empresa"]');
        if (rifInput) rifInput.value = rifTipo + rifNumero;
    }

    document.getElementById('modalEmpresa').addEventListener('submit', function(e) {
      setRifForEmpresa(this);
    });

    document.getElementById('modalEditEmpresa').addEventListener('submit', function(e) {
      setRifForEmpresa(this);
    });

    if (typeof toggleModal === "function") window.toggleModal = toggleModal;
    if (typeof closeDetalleModal === "function") window.closeDetalleModal = closeDetalleModal;
    if (typeof openEditModal === "function") window.openEditModal = openEditModal;
    if (typeof viewDetails === "function") window.viewDetails = viewDetails;
    if (typeof handleEditFromDetail === "function") window.handleEditFromDetail = handleEditFromDetail;
    if (typeof handleDeleteFromDetail === "function") window.handleDeleteFromDetail = handleDeleteFromDetail;
    if (typeof closeEditModal === "function") window.closeEditModal = closeEditModal;
    if (typeof confirmDelete === "function") window.confirmDelete = confirmDelete;
    if (typeof setRifForEmpresa === "function") window.setRifForEmpresa = setRifForEmpresa;
  });
})(window, document, window.jQuery);
