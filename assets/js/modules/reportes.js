(function (window, document, $) {
  'use strict';

  $(function () {
    (function () {
      var period = document.getElementById('periodSelect');
      var start = document.getElementById('startDateWrap');
      var end = document.getElementById('endDateWrap');
      if (!period || !start || !end) return;
      function updateDateInputs() {
    var show = period.value === 'custom';
    start.classList.toggle('hidden', !show);
    end.classList.toggle('hidden', !show);
      }
      period.addEventListener('change', updateDateInputs);
      updateDateInputs();
    })();
  });
})(window, document, window.jQuery);
