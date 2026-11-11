(function () {
  const states = new WeakMap();

  function getOptionLabel(option) {
    return option.textContent.trim();
  }

  function attachSearchableSelect(input, select) {
    if (!input || !select) {
      return;
    }

    if (states.has(select)) {
      return states.get(select);
    }

    const container = input.parentElement;
    const list = document.createElement('div');
    let matches = [];
    let activeIndex = -1;

    container.classList.add('relative');
    select.classList.add('hidden');
    input.required = select.required;
    select.required = false;
    input.style.paddingLeft = '2.25rem';
    input.autocomplete = 'off';
    input.setAttribute('role', 'combobox');
    input.setAttribute('aria-autocomplete', 'list');
    input.setAttribute('aria-expanded', 'false');

    const icon = document.createElement('span');
    icon.style.position = 'absolute';
    icon.style.left = '0.75rem';
    icon.style.top = '50%';
    icon.style.transform = 'translateY(-50%)';
    icon.style.pointerEvents = 'none';
    icon.style.color = '#9ca3af';
    icon.setAttribute('aria-hidden', 'true');
    icon.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke-width="2"/><path d="m20 20-4-4" stroke-width="2" stroke-linecap="round"/></svg>';
    container.appendChild(icon);

    list.className = 'fixed z-[9999] hidden max-h-80 overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-xl';
    list.setAttribute('role', 'listbox');
    list.style.zIndex = '9999';
    document.body.appendChild(list);

    function positionList() {
      if (list.classList.contains('hidden')) {
        return;
      }

      const bounds = input.getBoundingClientRect();
      const gap = 4;
      const spaceBelow = window.innerHeight - bounds.bottom - gap;
      const spaceAbove = bounds.top - gap;
      const openAbove = spaceBelow < 180 && spaceAbove > spaceBelow;
      const availableSpace = openAbove ? spaceAbove : spaceBelow;
      const maxHeight = Math.max(80, Math.min(320, availableSpace));

      list.style.left = `${bounds.left}px`;
      list.style.width = `${bounds.width}px`;
      list.style.maxHeight = `${maxHeight}px`;
      list.style.top = openAbove
        ? `${Math.max(gap, bounds.top - gap - list.offsetHeight)}px`
        : `${bounds.bottom + gap}px`;
    }

    function closeList() {
      list.classList.add('hidden');
      input.setAttribute('aria-expanded', 'false');
      activeIndex = -1;
    }

    function choose(option) {
      select.value = option.value;
      input.value = getOptionLabel(option);
      input.setCustomValidity('');
      closeList();
      select.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function render() {
      const query = input.value.trim().toLocaleLowerCase();
      matches = [...select.options].filter(option =>
        option.value !== '' &&
        (getOptionLabel(option).toLocaleLowerCase().includes(query) ||
          option.value.toLocaleLowerCase().includes(query))
      );
      list.replaceChildren();

      if (matches.length === 0) {
        const emptyMessage = document.createElement('div');
        emptyMessage.className = 'px-3 py-2 text-sm text-gray-500';
        emptyMessage.textContent = 'No se encontraron coincidencias';
        list.appendChild(emptyMessage);
      } else {
        matches.forEach((option, index) => {
          const item = document.createElement('button');
          item.type = 'button';
          item.className = 'block w-full px-3 py-2 text-left text-sm font-semibold text-navy-dark hover:bg-orange/10 focus:bg-orange/10 focus:outline-none';
          item.textContent = getOptionLabel(option);
          item.setAttribute('role', 'option');
          item.setAttribute('aria-selected', String(index === activeIndex));
          item.addEventListener('mousedown', event => event.preventDefault());
          item.addEventListener('click', () => choose(option));
          list.appendChild(item);
        });
      }

      list.classList.remove('hidden');
      input.setAttribute('aria-expanded', 'true');
      positionList();
    }

    function reset() {
      select.value = '';
      input.value = '';
      input.setCustomValidity('');
      closeList();
    }

    function sync() {
      const selected = select.selectedOptions[0];
      input.value = selected && selected.value !== '' ? getOptionLabel(selected) : '';
      input.setCustomValidity(input.required && !select.value ? 'Seleccione una opción de la lista.' : '');
      closeList();
    }

    input.addEventListener('focus', render);
    input.addEventListener('input', () => {
      const teniaSeleccion = Boolean(select.value);
      select.value = '';
      input.setCustomValidity(input.required ? 'Seleccione una opción de la lista.' : '');
      activeIndex = -1;

      // Notifica el borrado de la selección para limpiar datos dependientes
      // (stock, precio, unidad y subtotal) sin esperar a elegir otro producto.
      if (teniaSeleccion) {
        select.dispatchEvent(new Event('change', { bubbles: true }));
      }

      render();
    });
    input.addEventListener('keydown', event => {
      if (event.key === 'Escape') {
        closeList();
        return;
      }
      if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();
        if (list.classList.contains('hidden')) {
          render();
        }
        if (matches.length > 0) {
          const direction = event.key === 'ArrowDown' ? 1 : -1;
          activeIndex = activeIndex === -1
            ? (direction === 1 ? 0 : matches.length - 1)
            : (activeIndex + direction + matches.length) % matches.length;
          [...list.querySelectorAll('[role="option"]')].forEach((item, index) => {
            item.setAttribute('aria-selected', String(index === activeIndex));
            if (index === activeIndex) item.scrollIntoView({ block: 'nearest' });
          });
        }
        return;
      }
      if (event.key === 'Enter' && !list.classList.contains('hidden') && matches.length > 0) {
        event.preventDefault();
        choose(matches[activeIndex >= 0 ? activeIndex : 0]);
      }
    });
    select.addEventListener('change', sync);
    window.addEventListener('resize', positionList);
    window.addEventListener('scroll', positionList, true);
    if (select.form) {
      select.form.addEventListener('reset', () => window.setTimeout(reset, 0));
    }
    document.addEventListener('mousedown', event => {
      if (!container.contains(event.target) && !list.contains(event.target)) {
        closeList();
      }
    });

    const state = { reset, sync, render };
    states.set(select, state);
    sync();
    return state;
  }

  window.attachSearchableSelect = attachSearchableSelect;
  window.syncSearchableSelect = select => {
    const state = states.get(select);
    if (state) state.sync();
  };
  window.resetSearchableSelect = select => {
    const state = states.get(select);
    if (state) state.reset();
  };
  window.refreshSearchableSelect = select => {
    const state = states.get(select);
    if (state) {
      state.reset();
    }
  };
})();
