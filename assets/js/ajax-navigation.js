/**
 * Navegación AJAX del menú lateral de CopiGaby.
 * Carga el módulo solicitado y sustituye únicamente el área de trabajo,
 * manteniendo la barra lateral, la sesión y el historial del navegador.
 * No interviene en las peticiones CRUD existentes.
 */
(function (window, document) {
  'use strict';

  if (window.CopiGabyAjaxNavigation) return;

  var loadedScripts = new Set();
  var pendingScripts = new Map();
  var activeController = null;
  var navigationId = 0;

  function absoluteUrl(value, base) {
    return new URL(value, base || window.location.href);
  }

  function rememberLoadedScripts() {
    document.querySelectorAll('script[src]').forEach(function (script) {
      try {
        loadedScripts.add(absoluteUrl(script.getAttribute('src')).href);
      } catch (error) {
        // Una ruta de script inválida no debe impedir la navegación.
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', rememberLoadedScripts, { once: true });
  } else {
    rememberLoadedScripts();
  }

  function findContentShell(root) {
    if (!root || !root.body) return null;
    return Array.prototype.find.call(root.body.children, function (element) {
      return element.classList && element.classList.contains('ml-[260px]');
    }) || null;
  }

  function replaceModuleContent(parsedDocument) {
    var currentAside = document.querySelector('body > aside');
    var incomingAside = parsedDocument.querySelector('body > aside');
    if (!currentAside || !incomingAside) return false;

    var oldNodes = Array.from(document.body.children).filter(function (element) {
      return element !== currentAside && !(element.tagName === 'SCRIPT' && element.dataset.copigabyNavigationScript === '1');
    });
    oldNodes.forEach(destroyDataTables);
    oldNodes.forEach(function (element) { element.remove(); });

    var incomingNodes = Array.from(parsedDocument.body.children).filter(function (element) {
      return element !== incomingAside && element.tagName !== 'SCRIPT';
    });
    if (!incomingNodes.length) return false;

    incomingNodes.forEach(function (element) {
      var clone = document.importNode(element, true);
      clone.querySelectorAll('script').forEach(function (script) { script.remove(); });
      document.body.appendChild(clone);
    });

    return true;
  }

  function destroyDataTables(root) {
    if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.DataTable) return;

    root.querySelectorAll('table[data-datatable="true"]').forEach(function (table) {
      if (window.jQuery.fn.DataTable.isDataTable(table)) {
        window.jQuery(table).DataTable().destroy();
      }
    });
  }

  function copySvgClasses(current, incoming) {
    var currentIcons = current.querySelectorAll('svg');
    var incomingIcons = incoming.querySelectorAll('svg');
    currentIcons.forEach(function (icon, index) {
      if (incomingIcons[index]) icon.setAttribute('class', incomingIcons[index].getAttribute('class') || '');
    });
  }

  function updateSidebar(incomingDocument) {
    var currentAside = document.querySelector('aside');
    var incomingAside = incomingDocument.querySelector('aside');
    if (!currentAside || !incomingAside) return;

    var incomingLinks = new Map();
    incomingAside.querySelectorAll('a[href]').forEach(function (link) {
      incomingLinks.set(link.getAttribute('href'), link);
    });

    currentAside.querySelectorAll('a[href]').forEach(function (link) {
      var incoming = incomingLinks.get(link.getAttribute('href'));
      if (!incoming) return;
      link.setAttribute('class', incoming.getAttribute('class') || '');
      copySvgClasses(link, incoming);
    });

    var currentParents = currentAside.querySelectorAll('.dropdown-parent');
    var incomingParents = incomingAside.querySelectorAll('.dropdown-parent');
    currentParents.forEach(function (parent, index) {
      if (incomingParents[index]) {
        parent.className = incomingParents[index].className;
      }
    });

    var currentToggles = currentAside.querySelectorAll('.dropdown-toggle');
    var incomingToggles = incomingAside.querySelectorAll('.dropdown-toggle');
    currentToggles.forEach(function (button, index) {
      if (!incomingToggles[index]) return;
      button.className = incomingToggles[index].className;
      copySvgClasses(button, incomingToggles[index]);
    });
  }

  function getTopLevelCallableNames(source) {
    var lines = source.split(/\r?\n/);
    var indents = lines
      .filter(function (line) { return line.trim() !== ''; })
      .map(function (line) { return line.match(/^\s*/)[0].length; });
    if (!indents.length) return [];

    var baseIndent = Math.min.apply(Math, indents);
    var names = [];

    lines.forEach(function (line) {
      var indentation = line.match(/^\s*/)[0].length;
      if (indentation !== baseIndent) return;

      var functionMatch = line.match(/^\s*(?:async\s+)?function\s*\*?\s+([A-Za-z_$][\w$]*)\s*\(/);
      if (functionMatch) names.push(functionMatch[1]);

      var arrowMatch = line.match(/^\s*(?:const|let|var)\s+([A-Za-z_$][\w$]*)\s*=\s*(?:async\s*)?(?:function\b|(?:\([^)]*\)|[A-Za-z_$][\w$]*)\s*=>)/);
      if (arrowMatch) names.push(arrowMatch[1]);
    });

    return Array.from(new Set(names));
  }

  function executeInlineScript(source) {
    if (!source || !source.trim()) return;

    var exports = getTopLevelCallableNames(source).map(function (name) {
      return 'if (typeof ' + name + ' === "function") window[' + JSON.stringify(name) + '] = ' + name + ';';
    }).join('\n');

    // Cada vista se ejecuta en su propio ámbito para evitar que los const/let
    // globales de un módulo choquen al volver a visitarlo. Las funciones usadas
    // por atributos onclick se exponen nuevamente en window.
    var wrappedSource = '(function (window, document) {\n"use strict";\n'
      + source + '\n' + exports + '\n}).call(window, window, document);';

    var script = document.createElement('script');
    script.setAttribute('data-copigaby-navigation-inline', '1');
    script.textContent = wrappedSource;
    document.body.appendChild(script);
    script.remove();
  }

  function loadExternalScript(source, baseUrl, forceReload) {
    var url;
    try {
      url = absoluteUrl(source, baseUrl).href;
    } catch (error) {
      return Promise.reject(new Error('Ruta de script no válida: ' + source));
    }

    if (!forceReload && loadedScripts.has(url)) return Promise.resolve();
    if (pendingScripts.has(url)) return pendingScripts.get(url);

    var promise = new Promise(function (resolve, reject) {
      var script = document.createElement('script');
      script.src = url;
      script.async = false;
      script.dataset.copigabyNavigationScript = '1';
      script.onload = function () {
        loadedScripts.add(url);
        resolve();
      };
      script.onerror = function () {
        reject(new Error('No se pudo cargar el script del módulo: ' + source));
      };
      document.head.appendChild(script);
    }).finally(function () {
      pendingScripts.delete(url);
    });

    pendingScripts.set(url, promise);
    return promise;
  }

  function scriptNeedsReinitialization(source) {
    return /(?:^|\/)(?:quick-category|datatables-init)\.js(?:\?|$)/i.test(source || '');
  }

  async function executePageScripts(parsedDocument, targetUrl) {
    var incomingShell = findContentShell(parsedDocument);
    var scripts = Array.from(parsedDocument.querySelectorAll('script')).filter(function (script) {
      if (script.closest('aside')) return false;

      // Los scripts compartidos del menú están antes del contenedor del módulo.
      // Solo ejecutamos scripts del encabezado de la vista o posteriores al shell.
      var belongsToShell = incomingShell && incomingShell.contains(script);
      var followsShell = incomingShell
        && (incomingShell.compareDocumentPosition(script) & Node.DOCUMENT_POSITION_FOLLOWING);
      if (!belongsToShell && !followsShell) return false;

      var type = (script.getAttribute('type') || '').trim().toLowerCase();
      return !type || type === 'text/javascript' || type === 'application/javascript';
    });

    // El layout comparte este helper, pero sus campos cambian en cada módulo.
    // Se vuelve a ejecutar sobre los nuevos controles después de insertar la vista.
    var telephoneHelper = parsedDocument.querySelector('script[src*="telefono-input.js"]');
    if (telephoneHelper) {
      await loadExternalScript(telephoneHelper.getAttribute('src'), targetUrl, true);
    }

    for (var index = 0; index < scripts.length; index++) {
      var original = scripts[index];
      var source = original.getAttribute('src');
      if (source) {
        await loadExternalScript(source, targetUrl, scriptNeedsReinitialization(source));
      } else {
        executeInlineScript(original.textContent || '');
      }
    }
  }

  function cleanFloatingControls() {
    document.querySelectorAll('[role="listbox"]').forEach(function (element) {
      element.remove();
    });
  }

  async function navigate(url, options) {
    options = options || {};
    var targetUrl = absoluteUrl(url);
    if (targetUrl.origin !== window.location.origin) return false;

    var requestedModule = targetUrl.searchParams.get('url');
    if (!requestedModule || requestedModule === 'login') return false;

    if (activeController) activeController.abort();
    var controller = new AbortController();
    activeController = controller;
    var currentId = ++navigationId;
    var currentShell = findContentShell(document);
    if (currentShell) currentShell.setAttribute('aria-busy', 'true');

    try {
      var response = await window.fetch(targetUrl.href, {
        method: 'GET',
        credentials: 'same-origin',
        cache: 'no-store',
        signal: controller.signal,
        headers: {
          'Accept': 'text/html',
          'X-Requested-With': 'XMLHttpRequest',
          'X-Ajax-Navigation': '1'
        }
      });

      if (currentId !== navigationId) return false;
      if (!response.ok) throw new Error('No se pudo cargar el módulo solicitado.');

      var responseUrl = response.url || targetUrl.href;
      var parsedDocument = new DOMParser().parseFromString(await response.text(), 'text/html');
      var incomingShell = findContentShell(parsedDocument);

      // Si el servidor redirigió al login, devolvemos el control a la navegación normal.
      if (!incomingShell) {
        window.location.assign(responseUrl);
        return false;
      }

      cleanFloatingControls();
      if (!replaceModuleContent(parsedDocument)) {
        window.location.assign(responseUrl);
        return false;
      }

      document.body.className = parsedDocument.body.className || document.body.className;
      document.title = parsedDocument.title || document.title;
      updateSidebar(parsedDocument);

      if (!options.popstate) {
        window.history.pushState({ copigabyAjaxNavigation: true }, '', targetUrl.href);
      }

      await executePageScripts(parsedDocument, responseUrl);

      var newShellCurrent = findContentShell(document);
      if (newShellCurrent) newShellCurrent.removeAttribute('aria-busy');

      if (typeof window.CopiGabyDataTablesInit === 'function' && newShellCurrent) {
        await window.CopiGabyDataTablesInit(newShellCurrent);
      }

      document.dispatchEvent(new CustomEvent('copigaby:navigated', {
        detail: { url: targetUrl.href, module: requestedModule, content: newShellCurrent }
      }));
      window.scrollTo(0, 0);
      return true;
    } catch (error) {
      if (error.name === 'AbortError' || currentId !== navigationId) return false;
      console.error('No se pudo completar la navegación AJAX. Se usará la navegación normal.', error);
      window.location.assign(targetUrl.href);
      return false;
    } finally {
      if (currentId === navigationId) {
        var shell = findContentShell(document);
        if (shell) shell.removeAttribute('aria-busy');
        activeController = null;
      }
    }
  }

  document.addEventListener('click', function (event) {
    if (event.defaultPrevented || event.button !== 0) return;
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

    var anchor = event.target.closest && event.target.closest('aside a[href]');
    if (!anchor || anchor.target === '_blank' || anchor.hasAttribute('download')) return;
    if (anchor.dataset.ajaxNav === 'off') return;

    var targetUrl;
    try {
      targetUrl = absoluteUrl(anchor.getAttribute('href'));
    } catch (error) {
      return;
    }

    if (targetUrl.origin !== window.location.origin) return;
    if (!targetUrl.searchParams.get('url') || targetUrl.searchParams.get('url') === 'login') return;

    event.preventDefault();
    navigate(targetUrl.href);
  });

  window.addEventListener('popstate', function () {
    var targetUrl = new URL(window.location.href);
    if (targetUrl.searchParams.get('url') && targetUrl.searchParams.get('url') !== 'login') {
      navigate(targetUrl.href, { popstate: true });
    } else {
      window.location.reload();
    }
  });

  window.CopiGabyAjaxNavigation = Object.freeze({ navigate: navigate });
})(window, document);
