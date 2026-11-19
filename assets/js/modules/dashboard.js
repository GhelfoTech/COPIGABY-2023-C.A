(function (window, document, $) {
  'use strict';

  $(function () {
    window.CopiGabyShowModuleFlash();

    const indicadores = document.getElementById('dashboardIndicadores');
    const tasa = document.getElementById('dashboardTasa');
    if (indicadores && tasa) tasa.before(indicadores);
  });
})(window, document, window.jQuery);
