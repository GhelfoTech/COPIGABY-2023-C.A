<?php
$telefonoFieldConfig = $telefonoFieldConfig ?? [];
$telefonoFieldId = (string) ($telefonoFieldConfig['id'] ?? 'telefono');
$telefonoFieldName = (string) ($telefonoFieldConfig['name'] ?? 'telefono');
$telefonoFieldValue = (string) ($telefonoFieldConfig['value'] ?? '');
?>
<div class="flex w-full" data-telefono-field>
  <select data-telefono-prefix aria-label="Prefijo telefónico" class="shrink-0 rounded-l-lg border border-r-0 bg-gray-50 px-2 py-2 text-sm font-black text-navy-dark outline-none focus:border-orange">
    <option value="0426">0426</option>
    <option value="0416">0416</option>
    <option value="0424">0424</option>
    <option value="0414">0414</option>
    <option value="0412">0412</option>
    <option value="0422">0422</option>
  </select>
  <input type="tel" id="<?= htmlspecialchars($telefonoFieldId . '_numero', ENT_QUOTES, 'UTF-8') ?>" data-telefono-number inputmode="numeric" pattern="[0-9]{7}" maxlength="7" required placeholder="1234567" class="min-w-0 w-full rounded-r-lg border bg-gray-50 px-3 py-2 text-sm font-bold outline-none focus:border-orange">
  <input type="hidden" id="<?= htmlspecialchars($telefonoFieldId, ENT_QUOTES, 'UTF-8') ?>" name="<?= htmlspecialchars($telefonoFieldName, ENT_QUOTES, 'UTF-8') ?>" data-telefono-value value="<?= htmlspecialchars($telefonoFieldValue, ENT_QUOTES, 'UTF-8') ?>">
</div>
<script>
(function () {
  // Sincronización nativa e independiente de otros scripts: garantiza que el
  // campo oculto `telefono` siempre lleve prefijo + 7 dígitos al enviar.
  function init(field) {
    if (field.dataset.telefonoSyncInit) return;
    field.dataset.telefonoSyncInit = '1';

    var prefix = field.querySelector('[data-telefono-prefix]');
    var number = field.querySelector('[data-telefono-number]');
    var value = field.querySelector('[data-telefono-value]');
    if (!prefix || !number || !value) return;

    function sync() {
      number.value = number.value.replace(/\D/g, '').slice(0, 7);
      value.value = prefix.value + number.value;
    }

    // Asegura que al abrir la página el valor oculto refleje el prefijo actual.
    if (!value.value) value.value = prefix.value;

    prefix.addEventListener('change', sync);
    number.addEventListener('input', sync);
    number.addEventListener('blur', sync);

    var form = field.closest('form');
    if (form) {
      form.addEventListener('submit', sync);
      form.addEventListener('reset', function () {
        window.setTimeout(function () {
          prefix.value = '0426';
          number.value = '';
          sync();
        }, 0);
      });
    }
    sync();
  }

  function initAll(root) {
    (root || document).querySelectorAll('[data-telefono-field]').forEach(init);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { initAll(document); }, { once: true });
  } else {
    initAll(document);
  }

  // Los modales se insertan dinámicamente (navegación AJAX); observa el DOM.
  new MutationObserver(function () { initAll(document); }).observe(document.body, { childList: true, subtree: true });
})();
</script>
<?php unset($telefonoFieldConfig, $telefonoFieldId, $telefonoFieldName, $telefonoFieldValue); ?>
