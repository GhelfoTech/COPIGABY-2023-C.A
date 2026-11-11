(function () {
  const modal = document.getElementById('modalQuickCategory');
  const form = document.getElementById('formQuickCategory');
  const button = document.getElementById('btnSaveQuickCategory');
  if (!modal || !form || !button) return;

  let targetSelectId = '';

  window.openQuickCategoryModal = function (selectId) {
    targetSelectId = selectId;
    form.reset();
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.getElementById('quickCategoryName').focus();
  };

  function closeModal() {
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
  }

  modal.querySelectorAll('[data-close-quick-category]').forEach(element => {
    element.addEventListener('click', closeModal);
  });

  form.addEventListener('submit', async event => {
    event.preventDefault();
    const originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Guardando...';

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body: new FormData(form),
      });
      const result = await response.json();
      if (!response.ok || result.status !== 'success' || !result.categoria) {
        throw new Error(result.message || 'No se pudo registrar la categoría.');
      }

      const select = document.getElementById(targetSelectId);
      if (!select) throw new Error('No se encontró el selector de categoría.');
      const id = String(result.categoria.codigo_categoria);
      let option = [...select.options].find(item => item.value === id);
      if (!option) {
        option = new Option(result.categoria.nombre_categoria, id, true, true);
        select.add(option);
      }
      option.textContent = result.categoria.nombre_categoria;
      select.value = id;
      window.syncSearchableSelect(select);
      closeModal();
      form.reset();
      showAlert('La categoría fue agregada y quedó seleccionada.', 'success', 'Categoría añadida');
    } catch (error) {
      showAlert(error.message || 'No se pudo registrar la categoría.', 'error', 'Error');
    } finally {
      button.disabled = false;
      button.textContent = originalText;
    }
  });
})();
