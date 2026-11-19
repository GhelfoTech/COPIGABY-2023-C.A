(function ($, window, document) {
  'use strict';

  $(function () {
    window.CopiGabyShowModuleFlash();

    $(document)
      .off('.copigabyLayout')
      .on('click.copigabyLayout', '[data-js-action]', function () {
        var $button = $(this);
        var actionName = $button.attr('data-js-action');
        var action = window[actionName];
        var rawArguments = $button.attr('data-js-args');
        var argumentsList = [];

        if (rawArguments) {
          try {
            argumentsList = JSON.parse(rawArguments);
          } catch (error) {
            console.error('Los argumentos de la acción no son válidos: ' + actionName, error);
            return;
          }
        }

        if (typeof action !== 'function') {
          console.error('No existe la acción solicitada: ' + actionName);
          return;
        }

        if (actionName === 'submitCompra') {
          var form = document.getElementById('formCompra');
          if (form && window.CopiGabyValidaciones && !window.CopiGabyValidaciones.validarFormulario(form)) return;
        }

        action.apply(this, argumentsList);
      })
      .on('click.copigabyLayout', '#btnAyudaManual', function () {
        Swal.fire({
          title: 'Ayuda del sistema',
          html:
            '<p style="font-size:1rem;line-height:1.7;color:#374151">Si necesita orientación sobre el funcionamiento de los procesos, módulos o la navegación general del sistema, ' +
            'le invitamos a consultar nuestro <b>manual de usuario</b>.</p>' +
            '<p style="font-size:1rem;line-height:1.7;color:#374151">Descargue la guía completa para conocer el detalle de cada interfaz, botones de acción y flujos de trabajo.</p>',
          icon: 'info',
          showCancelButton: true,
          confirmButtonText: '📥 Descargar Manual',
          confirmButtonColor: '#3085d6',
          cancelButtonText: 'Cancelar',
          cancelButtonColor: '#aaa',
          reverseButtons: true,
          focusConfirm: false
        }).then(function (result) {
          if (result.isConfirmed) {
            window.location.assign('?url=ayuda&descargar=pdf');
          }
        });
      })
      .on('click.copigabyLayout', '.dropdown-toggle', function () {
        var $parent = $(this).closest('.dropdown-parent');
        var willOpen = !$parent.hasClass('open');
        $('.dropdown-parent').removeClass('open');
        if (willOpen) $parent.addClass('open');
      })
      .on('submit.copigabyLayout', 'form[method="POST"]', function (event) {
        if (event.isDefaultPrevented()) return;

        var $form = $(this);
        if ($form.data('submitting') === true) {
          event.preventDefault();
          return;
        }

        $form.data('submitting', true);
        $form.find('button[type="submit"]').prop('disabled', true)
          .addClass('opacity-60 cursor-not-allowed');
      });
  });
})(window.jQuery, window, document);
