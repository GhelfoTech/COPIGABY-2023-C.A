/**
 * Configuración AJAX global de CopiGaby.
 * Centraliza las peticiones fetch, intercepta formularios POST normales y
 * actualiza el <main> actual sin recargar el documento completo.
 */
(function (window, document) {
  'use strict';

  if (window.CopiGabyAjax) return;

  var nativeFetch = window.fetch.bind(window);
  var refreshInProgress = null;

  function isSameOrigin(url) {
    try {
      return new URL(url, window.location.href).origin === window.location.origin;
    } catch (error) {
      return false;
    }
  }

  function request(url, options) {
    options = options || {};
    var method = String(options.method || (options.body ? 'POST' : 'GET')).toUpperCase();
    var headers = new Headers(options.headers || {});
    var body = options.body;

    if (method !== 'GET' && method !== 'HEAD') {
      headers.set('X-Requested-With', 'XMLHttpRequest');
      if (!headers.has('Accept')) headers.set('Accept', 'application/json');

      if (body instanceof FormData && !body.has('_ajax_request')) {
        body.set('_ajax_request', '1');
      } else if (body instanceof URLSearchParams && !body.has('_ajax_request')) {
        body.set('_ajax_request', '1');
      }
    }

    return nativeFetch(url, Object.assign({}, options, {
      method: method,
      headers: headers,
      body: body,
      credentials: options.credentials || 'same-origin'
    }));
  }

  function sameNodeType(current, incoming) {
    if (!current || !incoming || current.nodeType !== incoming.nodeType) return false;
    if (current.nodeType === Node.ELEMENT_NODE) {
      return current.tagName === incoming.tagName;
    }
    return true;
  }

  function nodeKey(node) {
    if (!node || node.nodeType !== Node.ELEMENT_NODE) return null;
    if (node.id) return 'id:' + node.id;
    var customKey = node.getAttribute('data-ajax-key');
    return customKey ? 'key:' + customKey : null;
  }

  function morphNode(current, incoming) {
    if (!sameNodeType(current, incoming)) {
      if (current.parentNode) {
        current.parentNode.replaceChild(document.importNode(incoming, true), current);
      }
      return;
    }

    if (current.nodeType === Node.TEXT_NODE || current.nodeType === Node.COMMENT_NODE) {
      if (current.nodeValue !== incoming.nodeValue) current.nodeValue = incoming.nodeValue;
      return;
    }

    if (current.tagName === 'SCRIPT') return;

    var existingAttributes = Array.prototype.slice.call(current.attributes);
    existingAttributes.forEach(function (attribute) {
      if (!incoming.hasAttribute(attribute.name)) {
        current.removeAttribute(attribute.name);
      }
    });

    Array.prototype.slice.call(incoming.attributes).forEach(function (attribute) {
      if (current.getAttribute(attribute.name) !== attribute.value) {
        current.setAttribute(attribute.name, attribute.value);
      }
    });

    // Se conservan los valores escritos por el usuario en controles que no
    // participaron en la operación; el formulario enviado se limpia aparte.
    morphChildren(current, incoming);
  }

  function morphChildren(currentParent, incomingParent) {
    var oldChildren = Array.prototype.slice.call(currentParent.childNodes);
    var newChildren = Array.prototype.slice.call(incomingParent.childNodes);
    var retained = [];
    var cursor = currentParent.firstChild;

    newChildren.forEach(function (incomingChild) {
      var key = nodeKey(incomingChild);
      var match = null;

      if (key) {
        match = oldChildren.find(function (child) {
          return child.parentNode === currentParent && nodeKey(child) === key;
        }) || null;
      }

      if (!match && sameNodeType(cursor, incomingChild)) {
        match = cursor;
      }

      if (!match) {
        match = oldChildren.find(function (child) {
          return child.parentNode === currentParent
            && retained.indexOf(child) === -1
            && sameNodeType(child, incomingChild);
        }) || null;
      }

      if (match) {
        if (match !== cursor) currentParent.insertBefore(match, cursor);
        morphNode(match, incomingChild);
      } else {
        match = document.importNode(incomingChild, true);
        currentParent.insertBefore(match, cursor);
      }

      retained.push(match);
      cursor = match.nextSibling;
    });

    Array.prototype.slice.call(currentParent.childNodes).forEach(function (child) {
      if (retained.indexOf(child) === -1) child.remove();
    });
  }

  function destroyDataTables(main) {
    if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.DataTable) return;

    main.querySelectorAll('table[data-datatable="true"]').forEach(function (table) {
      if (window.jQuery.fn.DataTable.isDataTable(table)) {
        window.jQuery(table).DataTable().destroy();
      }
    });
  }

  async function refresh() {
    if (refreshInProgress) return refreshInProgress;

    refreshInProgress = (async function () {
      var currentMain = document.querySelector('main');
      if (!currentMain) throw new Error('No se encontró el contenido principal para actualizar.');

      var response = await nativeFetch(window.location.href, {
        method: 'GET',
        credentials: 'same-origin',
        cache: 'no-store',
        headers: { 'Accept': 'text/html' }
      });

      if (!response.ok) throw new Error('No se pudo actualizar el contenido del módulo.');

      var html = await response.text();
      var parsed = new DOMParser().parseFromString(html, 'text/html');
      var incomingMain = parsed.querySelector('main');

      if (!incomingMain) {
        throw new Error('La respuesta del servidor no contiene el contenido del módulo.');
      }

      destroyDataTables(currentMain);
      morphNode(currentMain, incomingMain);

      if (typeof window.CopiGabyDataTablesInit === 'function') {
        await window.CopiGabyDataTablesInit(currentMain);
      }

      document.dispatchEvent(new CustomEvent('copigaby:refreshed', {
        detail: { main: currentMain }
      }));

      return true;
    })();

    try {
      return await refreshInProgress;
    } finally {
      refreshInProgress = null;
    }
  }

  function restoreForm(form) {
    if (!form) return;
    form.dataset.ajaxProcessing = '0';
    form.dataset.submitting = '0';

    var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
    submitButtons.forEach(function (button) {
      button.disabled = false;
      button.classList.remove('opacity-60', 'cursor-not-allowed');
    });
  }

  function closeFormModal(form) {
    if (!form) return;
    var modal = form.closest('[id^="modal"], .fixed.inset-0');
    if (modal) {
      modal.classList.add('hidden');
      modal.setAttribute('aria-hidden', 'true');
    }
  }

  function isSuccessful(payload) {
    if (!payload || typeof payload !== 'object') return false;
    if (payload.status !== undefined) return payload.status === 'success';
    return payload.success === true;
  }

  async function readJson(response) {
    var text = await response.text();
    try {
      return JSON.parse(text);
    } catch (error) {
      throw new Error('El servidor no devolvió una respuesta JSON válida. Revise el controlador del módulo.');
    }
  }

  async function submitForm(form, submitter) {
    if (form.dataset.ajaxProcessing === '1') return;

    var action = form.action || window.location.href;
    if (!isSameOrigin(action)) return;

    form.dataset.ajaxProcessing = '1';

    // Los controladores usan los campos del formulario y el tipo de acción
    // de la URL; no dependemos del botón submit (el layout lo deshabilita).
    var data = new FormData(form);
    data.set('_ajax_request', '1');

    try {
      var response = await request(action, { method: 'POST', body: data });
      var payload = await readJson(response);

      if (!response.ok || !isSuccessful(payload)) {
        throw new Error(payload.message || payload.msj || 'No se pudo completar la operación.');
      }

      closeFormModal(form);
      form.reset();
      restoreForm(form);

      var message = payload.message || 'Operación realizada correctamente.';
      if (typeof window.showAlert === 'function') {
        await window.showAlert(message, 'success', 'Operación completada');
      }

      try {
        await refresh();
      } catch (refreshError) {
        console.error('La operación se completó, pero no se pudo actualizar la vista.', refreshError);
        if (typeof window.showAlert === 'function') {
          await window.showAlert('La operación se guardó, pero no se pudo refrescar el listado. Actualice la vista manualmente.', 'warning', 'Vista sin actualizar');
        }
      }
    } catch (error) {
      restoreForm(form);
      if (typeof window.showAlert === 'function') {
        await window.showAlert(error.message || 'Error de comunicación con el servidor.', 'error', 'No se pudo completar');
      } else {
        window.alert(error.message || 'Error de comunicación con el servidor.');
      }
    }
  }

  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (event.defaultPrevented) return; // Respeta los formularios que ya usan AJAX propio.
    if (String(form.method).toUpperCase() !== 'POST') return;
    if (form.dataset.ajax === 'off' || form.target === '_blank') return;

    event.preventDefault();
    submitForm(form, event.submitter || null);
  });

  window.CopiGabyAjax = {
    fetch: request,
    refresh: refresh
  };
})(window, document);
