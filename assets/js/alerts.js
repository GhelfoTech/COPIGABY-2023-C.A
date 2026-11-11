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
    .then(() => {
      if (success) window.location.reload();
      return success;
    });
}
