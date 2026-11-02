(() => {
  const tables = document.querySelectorAll('table[data-datatable="true"]');
  if (!tables.length) return;

  const loadScript = (src) => new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = src;
    script.onload = resolve;
    script.onerror = reject;
    document.head.appendChild(script);
  });

  const loadStylesheet = (href) => {
    return new Promise((resolve, reject) => {
      const existingLink = document.querySelector(`link[href="${href}"]`);
      if (existingLink) {
        if (existingLink.sheet) {
          resolve();
          return;
        }
        existingLink.addEventListener('load', resolve, { once: true });
        existingLink.addEventListener('error', reject, { once: true });
        return;
      }

      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = href;
      link.onload = resolve;
      link.onerror = () => reject(new Error(`No se pudo cargar la hoja de estilos: ${href}`));
      document.head.appendChild(link);
    });
  };

  const stylesReady = Promise.all([
    loadStylesheet('https://cdn.datatables.net/1.13.11/css/jquery.dataTables.min.css')
      .catch((error) => console.error('No se pudo cargar el estilo base de DataTables.', error)),
    loadStylesheet('https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css')
      .catch((error) => console.error('No se pudo cargar el estilo responsive de DataTables.', error)),
    loadStylesheet('assets/css/datatables-custom.css?v=2')
      .catch((error) => console.error('No se pudo cargar el estilo personalizado de DataTables.', error))
  ]);

  const initialize = () => {
    window.jQuery(tables).each(function () {
      if (window.jQuery.fn.DataTable.isDataTable(this)) return;
      window.jQuery(this).DataTable({
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
        autoWidth: false
      });
    });
  };

  const loadDataTables = () => loadScript('https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js')
    .then(() => loadScript('https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js'))
    .then(() => stylesReady)
    .then(initialize);

  if (window.jQuery) {
    if (window.jQuery.fn.DataTable) stylesReady.then(initialize);
    else loadDataTables();
  } else {
    loadScript('https://code.jquery.com/jquery-3.7.1.min.js').then(loadDataTables);
  }
})();
