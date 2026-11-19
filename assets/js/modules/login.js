(function ($) {
  'use strict';

  $(function () {
    var $toggle = $('#togglePassword');
    var $password = $('#password');
    if (!$toggle.length || !$password.length) return;

    $toggle.on('click', function () {
      var showPassword = $password.attr('type') === 'password';
      $password.attr('type', showPassword ? 'text' : 'password');
      $toggle.find('.eye-open').toggle(!showPassword);
      $toggle.find('.eye-closed').toggle(showPassword);
      $toggle.attr('aria-label', showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });
  });
})(window.jQuery);
