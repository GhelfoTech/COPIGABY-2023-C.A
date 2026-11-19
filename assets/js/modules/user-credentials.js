(function ($, window) {
  'use strict';

  $(function () {
    var $modal = $('#modalCredenciales');
    var $form = $('#formCredenciales');
    if (!$modal.length || !$form.length) return;

    function closeModal() {
      $modal.addClass('hidden');
    }

    $('#btnOpenCredenciales').off('click.userCredentials')
      .on('click.userCredentials', function () {
        $modal.removeClass('hidden');
      });
    $modal.off('click.userCredentials', '#btnCancelarCredenciales')
      .on('click.userCredentials', '#btnCancelarCredenciales', closeModal);
    $('#overlayCredenciales').off('click.userCredentials')
      .on('click.userCredentials', closeModal);

    $form.off('submit.userCredentials').on('submit.userCredentials', function (event) {
      event.preventDefault();
      var form = this;
      var nuevoNombre = String(new FormData(form).get('nuevo_nombre') || '').trim();

      window.CopiGabyAjax.request('?url=user&type=credenciales', {
        method: 'POST',
        body: new FormData(form),
        headers: { Accept: 'application/json' }
      })
        .then(function (response) { return response.json(); })
        .then(function (data) {
          if (data.status !== 'success') {
            window.showAlert(data.message || 'No se pudo actualizar las credenciales.', 'error', 'Error');
            return;
          }

          if (nuevoNombre) {
            $('#inicialUsuario').text(nuevoNombre.charAt(0).toUpperCase());
            $('#btnOpenCredenciales span').text('BIENVENIDO, ' + nuevoNombre.toUpperCase());
            $('#nombreUsuarioCredenciales').text('Usted está visualizando los datos de: ' + nuevoNombre.toUpperCase());
            ['username_actual', 'nuevo_nombre'].forEach(function (name) {
              var input = form.elements.namedItem(name);
              if (input) {
                input.value = nuevoNombre;
                input.defaultValue = nuevoNombre;
              }
            });
          }

          window.showAlert(data.message, 'success', 'Credenciales actualizadas');
          closeModal();
          form.reset();
        })
        .catch(function () {
          window.showAlert('Error de conexión. Intente nuevamente.', 'error', 'Error de conexión');
        });
    });

    $modal.off('click.userCredentials', '.toggle-pw')
      .on('click.userCredentials', '.toggle-pw', function () {
        var $button = $(this);
        var $input = $button.closest('.relative').find('input');
        if (!$input.length) return;

        var showPassword = $input.attr('type') === 'password';
        $input.attr('type', showPassword ? 'text' : 'password');
        $button.find('.eye-open').toggle(!showPassword);
        $button.find('.eye-closed').toggle(showPassword);
        $button.attr('aria-label', showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
      });
  });
})(window.jQuery, window);
