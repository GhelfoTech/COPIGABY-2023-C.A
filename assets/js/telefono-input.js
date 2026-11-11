(function () {
  const prefixes = ['0426', '0416', '0424', '0414', '0412', '0422'];

  function getElements(field) {
    return {
      prefix: field.querySelector('[data-telefono-prefix]'),
      number: field.querySelector('[data-telefono-number]'),
      value: field.querySelector('[data-telefono-value]'),
    };
  }

  function updateValue(field) {
    const { prefix, number, value } = getElements(field);
    if (!prefix || !number || !value) return;

    number.value = number.value.replace(/\D/g, '').slice(0, 7);
    value.value = prefix.value + number.value;
  }

  function setValue(field, phone) {
    const { prefix, number } = getElements(field);
    if (!prefix || !number) return;

    const digits = String(phone || '').replace(/\D/g, '');
    const matchingPrefix = prefixes.find(item => digits.startsWith(item));
    if (matchingPrefix) {
      prefix.value = matchingPrefix;
      number.value = digits.slice(matchingPrefix.length, 11);
    } else if (digits.length >= 4) {
      const legacyPrefix = digits.slice(0, 4);
      if (![...prefix.options].some(option => option.value === legacyPrefix)) {
        prefix.add(new Option(`${legacyPrefix} (actual)`, legacyPrefix));
      }
      prefix.value = legacyPrefix;
      number.value = digits.slice(4, 11);
    } else {
      prefix.value = prefixes[0];
      number.value = digits.slice(0, 7);
    }

    updateValue(field);
  }

  document.querySelectorAll('[data-telefono-field]').forEach(field => {
    const { prefix, number, value } = getElements(field);
    if (!prefix || !number || !value) return;

    if (value.value) {
      setValue(field, value.value);
    } else {
      prefix.value = prefixes[0];
      updateValue(field);
    }

    prefix.addEventListener('change', () => updateValue(field));
    number.addEventListener('input', () => updateValue(field));
    const form = field.closest('form');
    if (form) {
      form.addEventListener('reset', () => {
        window.setTimeout(() => {
          prefix.value = prefixes[0];
          number.value = '';
          updateValue(field);
        }, 0);
      });
    }
  });

  window.setTelefonoFieldValue = function (id, phone) {
    const value = document.getElementById(id);
    const field = value && value.closest('[data-telefono-field]');
    if (field) setValue(field, phone);
  };
})();
