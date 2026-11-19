/**
 * Configuración AJAX global de CopiGaby.
 * Centraliza las peticiones jQuery, intercepta formularios POST normales y
 * actualiza el <main> actual sin recargar el documento completo.
 */
(function (window, document, $) {
  'use strict';

  if (window.CopiGabyAjax) return;

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
    var method = 'POST';
    var data = options.data !== undefined ? options.data : options.body;
    var isFormData = data instanceof FormData;
    var headers = Object.assign({
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': options.dataType === 'html' ? 'text/html' : 'application/json'
    }, options.headers || {});

    if (isFormData) data.set('_ajax_request', '1');
    if (data && !isFormData && typeof data === 'object' && !(data instanceof URLSearchParams)) {
      data = $.extend({}, data);
      if (data._ajax_request === undefined) data._ajax_request = '1';
    }
    if (data instanceof URLSearchParams) {
      data.set('_ajax_request', '1');
      data = data.toString();
    }

    var ajaxOptions = {
      url: url,
      type: method,
      dataType: options.dataType || 'json',
      data: data,
      processData: !isFormData,
      contentType: isFormData ? false : (options.contentType || 'application/x-www-form-urlencoded; charset=UTF-8'),
      headers: headers,
      timeout: options.timeout || 0
    };

    if (options.signal) {
      if (options.signal.aborted) {
        return Promise.resolve(createResponse({ status: 0, responseText: '' }, null, 'abort'));
      }
      ajaxOptions.beforeSend = function (xhr) {
        options.signal.addEventListener('abort', function () { xhr.abort(); }, { once: true });
      };
    }

    return $.ajax(ajaxOptions).then(
      function (payload, status, xhr) {
        return createResponse(xhr, payload, status);
      },
      function (xhr, status) {
        return createResponse(xhr, null, status);
      }
    );
  }

  function createResponse(xhr, payload, status) {
    var responseText = xhr.responseText || '';
    var response = {
      ok: xhr.status >= 200 && xhr.status < 300,
      status: xhr.status || 0,
      json: function () {
        if (status === 'abort') {
          var abortError = new Error('La solicitud fue cancelada.');
          abortError.name = 'AbortError';
          return Promise.reject(abortError);
        }
        if (payload !== null && typeof payload === 'object') return Promise.resolve(payload);
        try {
          return Promise.resolve(JSON.parse(responseText || payload || 'null'));
        } catch (error) {
          return Promise.reject(new Error('El servidor no devolvió una respuesta JSON válida. Revise el controlador del módulo.'));
        }
      },
      text: function () {
        return Promise.resolve(responseText || (typeof payload === 'string' ? payload : JSON.stringify(payload || '')));
      }
    };

    if (status === 'abort') response.statusText = 'abort';
    return response;
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

      var html = await $.ajax({
        url: window.location.href,
        type: 'POST',
        dataType: 'html',
        headers: {
          'Accept': 'text/html',
          'X-Requested-With': 'XMLHttpRequest'
        }
      });
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

  $(document).on('submit.copigabyAjax', 'form[method="POST"]', function (event) {
    if (event.isDefaultPrevented()) return;
    if (this.dataset.ajax === 'off' || this.target === '_blank') return;

    event.preventDefault();
    submitForm(this, event.originalEvent && event.originalEvent.submitter || null);
  });

  window.CopiGabyAjax = {
    request: request,
    refresh: refresh
  };
})(window, document, window.jQuery);
