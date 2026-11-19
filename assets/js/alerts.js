function showAlert(message, icon = 'info', title = '') {
  return Swal.fire({
    icon,
    title,
    text: String(message)
  });
}

function showFlashAlert(status, message) {
  const isSuccess = status === 'success';
  return Swal.fire({
    icon: isSuccess ? 'success' : 'error',
    title: isSuccess ? 'Operación completada' : 'No se pudo completar',
    text: String(message)
  });
}

window.CopiGabyShowModuleFlash = function () {
  const element = document.querySelector('[data-module-flash-status], [data-system-flash-status]');
  if (!element) return Promise.resolve();

  const isSystemFlash = element.hasAttribute('data-system-flash-status');
  const statusAttribute = isSystemFlash ? 'data-system-flash-status' : 'data-module-flash-status';
  const messageAttribute = isSystemFlash ? 'data-system-flash-message' : 'data-module-flash-message';
  const status = element.getAttribute(statusAttribute);
  const message = element.getAttribute(messageAttribute);
  element.removeAttribute(statusAttribute);
  element.removeAttribute(messageAttribute);

  return showFlashAlert(status, message);
};

async function showConfirm(message, title = '¿Está seguro?', confirmButtonText = 'Sí, continuar') {
  const result = await Swal.fire({
    icon: 'warning',
    title,
    text: message,
    showCancelButton: true,
    confirmButtonText,
    cancelButtonText: 'Cancelar',
    reverseButtons: true
  });

  return result.isConfirmed;
}

function showMutationResultAndReload(result, successMessage, errorMessage) {
  const success = result && result.status === 'success';
  const message = result && result.message
    ? result.message
    : (success ? successMessage : errorMessage);

  return showAlert(message, success ? 'success' : 'error', success ? 'Operación completada' : 'Error')
    .then(async () => {
      if (success && window.CopiGabyAjax) {
        try {
          await window.CopiGabyAjax.refresh();
        } catch (error) {
          console.error('La operación se completó, pero no se pudo actualizar el módulo.', error);
        }
      }
      return success;
    });
}