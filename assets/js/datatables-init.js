(() => {
  'use strict';

  let jqueryPromise = null;
  let loadingPromise = null;
  let stylesPromise = null;

  const loadScript = (src) => new Promise((resolve, reject) => {
    const existing = document.querySelector(`script[data-datatable-src="${src}"]`);
    if (existing) {
      if (existing.dataset.loaded === '1') return resolve();
      existing.addEventListener('load', resolve, { once: true });
      existing.addEventListener('error', reject, { once: true });
      return;
    }

    const script = document.createElement('script');
    script.src = src;
    script.dataset.datatableSrc = src;
    script.onload = () => {
      script.dataset.loaded = '1';
      resolve();
    };
    script.onerror = () => reject(new Error(`No se pudo cargar el script: ${src}`));
    document.head.appendChild(script);
  });

  const loadStylesheet = (href) => new Promise((resolve, reject) => {
    const existing = document.querySelector(`link[data-datatable-href="${href}"]`);
    if (existing) {
      if (existing.dataset.loaded === '1' || existing.sheet) return resolve();
      existing.addEventListener('load', resolve, { once: true });
      existing.addEventListener('error', reject, { once: true });
      return;
    }

    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = href;
    link.dataset.datatableHref = href;
    link.onload = () => {
      link.dataset.loaded = '1';
      resolve();
    };
    link.onerror = () => reject(new Error(`No se pudo cargar la hoja de estilos: ${href}`));
    document.head.appendChild(link);
  });

  function loadStyles() {
    if (!stylesPromise) {
      stylesPromise = Promise.all([
        loadStylesheet('https://cdn.datatables.net/1.13.11/css/jquery.dataTables.min.css')
          .catch((error) => console.error('No se pudo cargar el estilo base de DataTables.', error)),
        loadStylesheet('https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css')
          .catch((error) => console.error('No se pudo cargar el estilo responsive de DataTables.', error)),
        loadStylesheet('assets/css/datatables-custom.css?v=3')
          .catch((error) => console.error('No se pudo cargar el estilo personalizado de DataTables.', error))
      ]);
    }
    return stylesPromise;
  }

  function ensureJQuery() {
    if (window.jQuery) return Promise.resolve();
    if (!jqueryPromise) {
      jqueryPromise = loadScript('https://code.jquery.com/jquery-3.7.1.min.js')
        .catch((error) => {
          jqueryPromise = null;
          throw error;
        });
    }
    return jqueryPromise;
  }

  function ensureDataTables() {
    return ensureJQuery().then(() => {
      if (window.jQuery.fn && window.jQuery.fn.DataTable) return loadStyles();
      if (!loadingPromise) {
        loadingPromise = loadScript('https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js')
          .then(() => loadScript('https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js'))
          .then(loadStyles)
          .catch((error) => {
            loadingPromise = null;
            throw error;
          });
      }
      return loadingPromise;
    });
  }

  function initialize(root) {
    root = root || document;
    const tables = root.querySelectorAll('table[data-datatable="true"]');
    if (!tables.length) return Promise.resolve();

    return ensureDataTables().then(() => {
      if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.DataTable) return;

      window.jQuery(tables).each(function () {
        if (window.jQuery.fn.DataTable.isDataTable(this)) return;
        const dataTable = window.jQuery(this).DataTable({
          responsive: true,
          language: {
            decimal: '',
            emptyTable: 'No hay datos disponibles en la tabla',
            info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            infoEmpty: 'Mostrando 0 a 0 de 0 registros',
            infoFiltered: '(filtrado de _MAX_ registros totales)',
            infoPostFix: '',
            thousands: '.',
            lengthMenu: 'Mostrar _MENU_ registros',
            loadingRecords: 'Cargando...',
            processing: 'Procesando...',
            search: 'Buscar:',
            searchPlaceholder: 'Buscar registros',
            zeroRecords: 'No se encontraron registros coincidentes',
            paginate: {
              first: 'Primero',
              last: 'Último',
              next: 'Siguiente',
              previous: 'Anterior'
            },
            aria: {
              sortAscending: ': activar para ordenar la columna de manera ascendente',
              sortDescending: ': activar para ordenar la columna de manera descendente'
            }
          },
          pageLength: 10,
          lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
          order: [],
          autoWidth: true
        });

        window.requestAnimationFrame(() => {
          dataTable.columns.adjust();
          if (dataTable.responsive) dataTable.responsive.recalc();
        });
      });
    });
  }

  // Exportada para que el refresco AJAX global pueda reactivar tablas nuevas.
  window.CopiGabyDataTablesInit = initialize;

  if (document.querySelector('table[data-datatable="true"]')) {
    initialize(document).catch((error) => console.error('No se pudieron inicializar las tablas.', error));
  }
})();
