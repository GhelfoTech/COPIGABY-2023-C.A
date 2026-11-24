
(function ($, window, document) {
  'use strict';

  const PATRONES = window.CopiGabiRegex;
  if (!PATRONES) {
    console.error('CopiGabi: no se cargó assets/js/validaciones/regex.js.');
    return;
  }

  let contadorMensajes = 0;
  const CLASE_ERROR = 'cg-validation-error';
  const CLASE_INVALIDO = 'cg-field-invalid';

  const MENSAJES = {
    letras: 'Escribe solo letras y espacios. Ejemplo: María Pérez.',
    texto: 'Utiliza letras, números y signos comunes. Ejemplo: Papel Bond A4.',
    numeros: 'Este campo solo admite números. Ejemplo: 12345678.',
    cedula: 'La cédula debe tener entre 6 y 9 dígitos. Ejemplo: 12345678.',
    telefono: 'El teléfono debe contener 11 dígitos en total.',
    decimal: 'Introduce un número con hasta dos decimales y usa punto. Ejemplo: 125.50.',
    correo: 'Escribe un correo válido que termine en .com. Ejemplo: ventas@empresa.com.'
  };
  const PREFIJOS_TELEFONO = ['0426', '0416', '0424', '0414', '0412', '0422'];

  function obtenerCamposTelefono(grupo) {
    const $grupo = $(grupo);
    return {
      prefijo: $grupo.find('[data-telefono-prefix]').get(0),
      numero: $grupo.find('[data-telefono-number]').get(0),
      valor: $grupo.find('[data-telefono-value]').get(0)
    };
  }

  function actualizarValorTelefono(grupo) {
    const campos = obtenerCamposTelefono(grupo);
    if (!campos.prefijo || !campos.numero || !campos.valor) return;

    campos.numero.value = campos.numero.value.replace(/\D/g, '').slice(0, 7);
    campos.valor.value = campos.prefijo.value + campos.numero.value;
  }

  function establecerValorTelefono(grupo, telefono) {
    const campos = obtenerCamposTelefono(grupo);
    if (!campos.prefijo || !campos.numero) return;

    const digitos = String(telefono || '').replace(/\D/g, '');
    const prefijoCoincidente = PREFIJOS_TELEFONO.find(function (prefijo) {
      return digitos.startsWith(prefijo);
    });

    if (prefijoCoincidente) {
      campos.prefijo.value = prefijoCoincidente;
      campos.numero.value = digitos.slice(prefijoCoincidente.length, 11);
    } else if (digitos.length >= 4) {
      const prefijoAnterior = digitos.slice(0, 4);
      const existePrefijo = Array.from(campos.prefijo.options).some(function (opcion) {
        return opcion.value === prefijoAnterior;
      });
      if (!existePrefijo) {
        campos.prefijo.add(new Option(prefijoAnterior + ' (actual)', prefijoAnterior));
      }
      campos.prefijo.value = prefijoAnterior;
      campos.numero.value = digitos.slice(4, 11);
    } else {
      campos.prefijo.value = PREFIJOS_TELEFONO[0];
      campos.numero.value = digitos.slice(0, 7);
    }

    actualizarValorTelefono(grupo);
  }

  function inicializarCampoTelefono(grupo) {
    const $grupo = $(grupo);
    if ($grupo.data('telefonoInicializado')) return;

    const campos = obtenerCamposTelefono(grupo);
    if (!campos.prefijo || !campos.numero || !campos.valor) return;

    $grupo.data('telefonoInicializado', true);
    if (campos.valor.value) {
      establecerValorTelefono(grupo, campos.valor.value);
    } else {
      campos.prefijo.value = PREFIJOS_TELEFONO[0];
      actualizarValorTelefono(grupo);
    }

    $(campos.prefijo).off('change.cgValidation').on('change.cgValidation', function () {
      actualizarValorTelefono(grupo);
    });
    $(campos.numero).off('input.cgValidation').on('input.cgValidation', function () {
      actualizarValorTelefono(grupo);
    });

    const $formulario = $grupo.closest('form');
    if ($formulario.length) {
      $formulario.on('reset.cgValidation', function () {
        window.setTimeout(function () {
          campos.prefijo.value = PREFIJOS_TELEFONO[0];
          campos.numero.value = '';
          actualizarValorTelefono(grupo);
        }, 0);
      });
    }
  }

  function inicializarCamposTelefono(contenedor) {
    const $contenedor = $(contenedor);
    $contenedor.filter('[data-telefono-field]').add($contenedor.find('[data-telefono-field]'))
      .each(function () { inicializarCampoTelefono(this); });
  }

  window.setTelefonoFieldValue = function (id, telefono) {
    const grupo = $('#' + $.escapeSelector(id)).closest('[data-telefono-field]').get(0);
    if (grupo) establecerValorTelefono(grupo, telefono);
  };

  function cargarEstilos() {
    if ($('#copigaby-validaciones-css').length) return;
    $('<link>', {
      id: 'copigaby-validaciones-css',
      rel: 'stylesheet',
      href: 'assets/js/validaciones/validaciones.css'
    }).appendTo('head');
  }

  function valorDe(campo) {
    return String(campo.value == null ? '' : campo.value).trim();
  }

  function nombreCampo(campo) {
    return String(campo.name || campo.id || '').toLowerCase();
  }

  function esCampoEditable(campo) {
    if (!campo || campo.disabled || campo.readOnly) return false;
    if (campo.type === 'hidden') return false;
    if (['button', 'submit', 'reset', 'checkbox', 'radio', 'file', 'image'].includes(campo.type)) return false;
    return /^(INPUT|SELECT|TEXTAREA)$/.test(campo.tagName);
  }

  function esFilaHorizontal(elemento) {
    if (!elemento || !window.getComputedStyle) return false;

    const estilo = window.getComputedStyle(elemento);
    const esFlex = estilo.display === 'flex' || estilo.display === 'inline-flex';
    return esFlex && estilo.flexDirection !== 'column' && estilo.flexDirection !== 'column-reverse';
  }

  function contieneAdornoPosicionado(contenedor, campo) {
    if (!contenedor || !window.getComputedStyle) return false;

    return Array.from(contenedor.children).some(function (hijo) {
      if (hijo === campo || window.getComputedStyle(hijo).position !== 'absolute') {
        return false;
      }

      // Detecta iconos SVG, elementos decorativos accesibles y botones auxiliares
      // (por ejemplo, el ojo de contraseña) anclados al contenedor del input.
      return $(hijo).is('svg, i, button, [aria-hidden="true"], [class*="icon"]')
        || $(hijo).find('svg, i').length > 0;
    });
  }

  function obtenerAncla(campo) {
    // El componente telefónico tiene un grupo propio con prefijo y número.
    if (campo.matches('[data-telefono-number]')) {
      return campo.closest('[data-telefono-field]') || campo;
    }

    // Los formularios pueden declarar explícitamente el contenedor de un campo.
    const contenedorDeclarado = campo.closest('[data-validation-wrapper]');
    if (contenedorDeclarado) return contenedorDeclarado;

    // Los selectores buscables conservan un SELECT oculto. Su control visible
    // es el elemento inmediatamente anterior (buscador o fila del buscador).
    let controlVisual = campo;
    const estaOculto = campo.classList.contains('hidden')
      || (window.getComputedStyle && window.getComputedStyle(campo).display === 'none');
    if (estaOculto && campo.tagName === 'SELECT' && campo.previousElementSibling) {
      controlVisual = campo.previousElementSibling;
    }

    const padre = controlVisual.parentElement;
    let ancla = controlVisual;

    // Si el input comparte un contenedor con iconos/botones posicionados de forma
    // absoluta, la alerta debe colocarse DESPUÉS de ese contenedor, nunca dentro.
    // Así el alto de la alerta no altera el centro vertical del icono ni del ojo.
    if (contieneAdornoPosicionado(padre, controlVisual)) {
      ancla = padre;
    } else if (esFilaHorizontal(padre)) {
      ancla = padre;
    } else if (esFilaHorizontal(padre && padre.parentElement)) {
      ancla = padre.parentElement;
    }

    // Evita que el mensaje se convierta en un elemento lateral de otra fila flex.
    // Solo se sube mientras sea necesario salir de filas horizontales anidadas.
    while (ancla && esFilaHorizontal(ancla.parentElement)) {
      ancla = ancla.parentElement;
    }

    const formulario = campo.form;
    if (!ancla || ancla === formulario || ancla === document.body) return campo;
    return ancla;
  }

  function obtenerIdMensaje(campo) {
    if (!campo.dataset.cgValidationId) {
      contadorMensajes += 1;
      campo.dataset.cgValidationId = 'cg-validation-' + contadorMensajes;
    }
    return campo.dataset.cgValidationId;
  }

  function limpiarDescribedBy(campo, idMensaje) {
    const ids = (campo.getAttribute('aria-describedby') || '')
      .split(/\s+/)
      .filter(Boolean)
      .filter(id => id !== idMensaje);

    if (ids.length) campo.setAttribute('aria-describedby', ids.join(' '));
    else campo.removeAttribute('aria-describedby');
  }

  function limpiarEstado(campo) {
    const idMensaje = campo.dataset.cgValidationId;
    if (idMensaje) {
      $('#' + $.escapeSelector(idMensaje)).remove();
      limpiarDescribedBy(campo, idMensaje);
    }

    campo.classList.remove(CLASE_INVALIDO);
    campo.removeAttribute('aria-invalid');
  }

  function mostrarError(campo, mensaje) {
    limpiarEstado(campo);

    const idMensaje = obtenerIdMensaje(campo);
    const ancla = obtenerAncla(campo);
    let $elemento = $('#' + $.escapeSelector(idMensaje));

    if (!$elemento.length) {
      $elemento = $('<small>', {
        id: idMensaje,
        class: CLASE_ERROR,
        role: 'alert',
        'aria-live': 'polite'
      }).insertAfter(ancla);
    }

    $elemento.text(mensaje);
    $(campo).addClass(CLASE_INVALIDO).attr('aria-invalid', 'true');

    const ids = (campo.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
    if (!ids.includes(idMensaje)) ids.push(idMensaje);
    campo.setAttribute('aria-describedby', ids.join(' '));
  }

  function mostrarCorrecto(campo) {
    limpiarEstado(campo);
  }

  function mensajeRequerido(campo) {
    if (campo.tagName === 'SELECT') return 'Selecciona una opción para continuar.';
    const placeholder = campo.getAttribute('placeholder');
    if (placeholder && !/^[•*]+$/.test(placeholder.trim())) {
      return 'Completa este campo. Ejemplo: ' + placeholder + '.';
    }
    return 'Este campo es obligatorio. Complétalo para continuar.';
  }

  function esObligatorio(campo) {
    if (campo.required || campo.matches('[data-purchase-quantity], .item-cant')) return true;

    const formulario = campo.form;
    const accion = String(formulario && formulario.getAttribute('action') || '').toLowerCase();
    const nombre = nombreCampo(campo);
    const idFormulario = formulario ? formulario.id : '';

    // Algunos campos obligatorios del backend no tenían el atributo required en la vista.
    if (idFormulario === 'formClienteRegistro' && ['cedula_cliente', 'nombre', 'direccion'].includes(nombre)) return true;
    if (accion.includes('url=cliente') && accion.includes('type=update') && nombre === 'direccion') return true;
    if (accion.includes('url=producto') && ['nombre_producto', 'codigo_categoria', 'stock_actual', 'stock_minimo'].includes(nombre)) return true;
    if (accion.includes('url=medida') && ['nombre', 'abreviatura', 'cantidad_unidad'].includes(nombre)) return true;
    if (accion.includes('url=proveedor') && ['razon_social', 'rif_proveedor', 'correo'].includes(nombre)) return true;
    if (accion.includes('url=pedido') && ['codigo_cliente', 'fecha_pedido', 'codigo_iva', 'codigo_metodo'].includes(nombre)) return true;
    if (accion.includes('url=compra') && ['codigo_proveedor', 'numero_factura_proveedor', 'fecha_compra'].includes(nombre)) return true;
    return false;
  }

  function reglaTextoPorCampo(campo) {
    const nombre = nombreCampo(campo);
    const ruta = String(campo.form && campo.form.getAttribute('action') || '').toLowerCase();

    if (nombre === 'nombre') return ruta.includes('medida') ? 'texto' : 'letras';

    if (nombre === 'cedula_cliente') return 'cedula';
    if (nombre === 'cedula') return 'numeros'; // En Usuarios el backend exige solo dígitos, sin longitud fija.
    if (nombre === 'rif_proveedor') return 'numeros';
    if (nombre === 'rif_numero') return 'numeros';
    if (nombre === 'referencia_pago') return 'numeros';

    if (nombre === 'correo' || campo.type === 'email') return 'correo';

    if (nombre === 'nombre_usuario' || nombre === 'nuevo_nombre') return 'texto';
    if ([
      'nombre_empresa', 'razon_social', 'nombre_categoria', 'nombre_producto',
      'nombre_servicio', 'nombre_metodo', 'nombre_moneda', 'nombre_rol',
      'abreviatura', 'simbolo', 'direccion', 'descripcion', 'logo',
      'numero_factura_proveedor'
    ].includes(nombre)) return 'texto';

    if (campo.tagName === 'TEXTAREA' || campo.type === 'text' || campo.type === 'search' || campo.type === 'tel') {
      return 'texto';
    }

    return null;
  }

  function validarFormato(campo, valor) {
    const nombre = nombreCampo(campo);

    if (campo.validity && campo.validity.badInput) {
      return campo.type === 'date' ? 'Introduce una fecha válida.' : 'Introduce un valor válido en este campo.';
    }

    if (campo.matches('[data-telefono-number]')) {
      if (!PATRONES.numeros.test(valor) || valor.length !== 7) {
        return 'Escribe los 7 dígitos que siguen al prefijo telefónico.';
      }
      return null;
    }

    if (nombre === 'rif_numero' && valor.length !== 9) {
      return 'El RIF debe contener exactamente 9 dígitos después de la letra.';
    }

    if (nombre === 'cedula_cliente' && !PATRONES.cedula.test(valor)) return MENSAJES.cedula;

    if (nombre === 'correo' || campo.type === 'email') {
      if (!PATRONES.correo.test(valor) || !/\.com$/i.test(valor)) return MENSAJES.correo;
      return null;
    }

    const regla = reglaTextoPorCampo(campo);
    if (regla && PATRONES[regla] && !PATRONES[regla].test(valor)) {
      return MENSAJES[regla];
    }

    if (campo.type === 'date') {
      if (campo.min && valor < campo.min) return 'La fecha no puede ser anterior al ' + campo.min + '.';
      if (campo.max && valor > campo.max) return 'La fecha no puede ser posterior al ' + campo.max + '.';
    }

    if (campo.type === 'number') {
      if (!PATRONES.decimal.test(valor)) return MENSAJES.decimal;

      const numero = Number(valor);
      if (!Number.isFinite(numero)) return 'Introduce un número válido.';

      if (campo.min !== '' && numero < Number(campo.min)) {
        return 'El valor debe ser igual o mayor que ' + campo.min + '.';
      }
      if (campo.max !== '' && numero > Number(campo.max)) {
        return 'El valor debe ser igual o menor que ' + campo.max + '.';
      }

      const paso = campo.step;
      if (paso && paso !== 'any') {
        const step = Number(paso);
        const base = campo.min !== '' ? Number(campo.min) : 0;
        if (step > 0 && Math.abs((numero - base) / step - Math.round((numero - base) / step)) > 0.000001) {
          return step === 1
            ? 'Este campo requiere un número entero.'
            : 'El valor debe respetar el incremento de ' + step + '.';
        }
      }

      // En Pedidos, la cantidad de un producto no puede superar su stock disponible.
      if (campo.matches('[data-purchase-quantity], .item-cant')) {
        const fila = campo.closest('tr[data-item]');
        const tipoItem = fila && $(fila).find('.item-tipo').get(0);
        const selectorItem = fila && $(fila).find('.item-select').get(0);
        const opcionItem = selectorItem && selectorItem.options[selectorItem.selectedIndex];
        const stockTexto = opcionItem && opcionItem.getAttribute('data-stock');

        if (tipoItem && tipoItem.value === 'producto' && stockTexto !== null && stockTexto !== '') {
          const stockDisponible = Number(stockTexto);
          if (Number.isFinite(stockDisponible) && numero > stockDisponible) {
            return 'No hay stock.';
          }
        }
      }

      if (nombre === 'precio' && numero <= 0) return 'El precio debe ser mayor que cero.';
      if (['stock_actual', 'stock_minimo'].includes(nombre) && numero < 0) {
        return 'El stock no puede ser negativo.';
      }
      if (nombre === 'porcentaje_iva' && (numero <= 0 || numero > 100)) {
        return 'El porcentaje de IVA debe ser mayor que cero y hasta 100.';
      }
      return null;
    }

    const accionFormulario = String(campo.form && campo.form.getAttribute('action') || '').toLowerCase();
    if (nombre === 'password' && accionFormulario.includes('url=user')) {
      if (valor.length < 4) return 'La contraseña debe tener al menos 4 caracteres.';
    }

    // Respeta cualquier patrón HTML ya declarado en la vista.
    if (campo.hasAttribute('pattern')) {
      try {
        const patronHTML = new RegExp('^(?:' + campo.getAttribute('pattern') + ')$');
        if (!patronHTML.test(valor)) {
          if (nombre === 'rif_numero') return 'El RIF debe contener 9 dígitos.';
          if (campo.matches('[data-telefono-number]')) return 'Escribe los 7 dígitos después del prefijo telefónico.';
          return 'El formato de este campo no es válido.';
        }
      } catch (error) {
        // Un patrón HTML mal formado no debe romper los demás formularios.
      }
    }

    if (campo.maxLength > -1 && valor.length > campo.maxLength) {
      return 'Este campo admite como máximo ' + campo.maxLength + ' caracteres.';
    }
    if (campo.minLength > 0 && valor.length < campo.minLength) {
      return 'Este campo debe contener al menos ' + campo.minLength + ' caracteres.';
    }

    return null;
  }

  function validarCampo(campo, mostrarObligatorio) {
    if (!esCampoEditable(campo)) return true;

    const valor = valorDe(campo);

    if (campo.validity && campo.validity.customError) {
      const mensajePersonalizado = campo.validationMessage || 'El valor introducido no es válido.';
      mostrarError(campo, mensajePersonalizado);
      return false;
    }

    if (valor === '') {
      limpiarEstado(campo);
      if (esObligatorio(campo) && mostrarObligatorio) {
        mostrarError(campo, mensajeRequerido(campo));
        return false;
      }
      return true;
    }

    const mensaje = validarFormato(campo, valor);
    if (mensaje) {
      mostrarError(campo, mensaje);
      return false;
    }

    mostrarCorrecto(campo);
    return true;
  }

  function validarTelefono(formulario) {
    let valido = true;
    $(formulario).find('[data-telefono-field]').each(function () {
      const grupo = this;
      const campos = obtenerCamposTelefono(grupo);
      const prefijo = campos.prefijo;
      const numero = campos.numero;
      const valorCompleto = campos.valor;

      if (!prefijo || !numero || !valorCompleto || numero.disabled) return;

      const numeroValor = valorDe(numero);
      const completo = valorDe(valorCompleto);
      const campoObligatorio = numero.required;

      if (!numeroValor && !campoObligatorio) {
        limpiarEstado(numero);
        return;
      }

      if (!prefijo.value || !numeroValor) {
        if (campoObligatorio) {
          mostrarError(numero, 'Completa el prefijo y los 7 dígitos del teléfono.');
          valido = false;
        }
        return;
      }

      if (!PATRONES.numeros.test(numeroValor) || numeroValor.length !== 7 || !PATRONES.telefono.test(completo)) {
        mostrarError(numero, 'El teléfono debe tener un prefijo válido y 7 dígitos adicionales.');
        valido = false;
      }
    });
    return valido;
  }

  function validarCredenciales(formulario) {
    if (formulario.id !== 'formCredenciales') return true;

    let valido = true;
    const actual = formulario.elements.namedItem('password_actual');
    const nueva = formulario.elements.namedItem('nueva_password');
    const confirmar = formulario.elements.namedItem('confirmar_password');

    if (actual && !valorDe(actual)) {
      mostrarError(actual, 'Escribe tu contraseña actual para confirmar los cambios.');
      valido = false;
    }

    if (nueva && valorDe(nueva)) {
      if (!confirmar || !valorDe(confirmar)) {
        if (confirmar) mostrarError(confirmar, 'Confirma la nueva contraseña para continuar.');
        valido = false;
      } else if (valorDe(nueva) !== valorDe(confirmar)) {
        mostrarError(confirmar, 'Las contraseñas no coinciden. Vuelve a escribir la misma contraseña.');
        valido = false;
      }
    } else if (confirmar && valorDe(confirmar)) {
      mostrarError(confirmar, 'Primero escribe la nueva contraseña y luego confírmala.');
      valido = false;
    }

    return valido;
  }

  function validarRangoReporte(formulario) {
    const $formulario = $(formulario);
    const period = $formulario.find('#periodSelect').get(0);
    if (!period || period.value !== 'custom') return true;

    const desde = $formulario.find('[name="start_date"]').get(0);
    const hasta = $formulario.find('[name="end_date"]').get(0);
    let valido = true;

    if (!desde || !desde.value) {
      if (desde) mostrarError(desde, 'Selecciona la fecha inicial del rango personalizado.');
      valido = false;
    }
    if (!hasta || !hasta.value) {
      if (hasta) mostrarError(hasta, 'Selecciona la fecha final del rango personalizado.');
      valido = false;
    }
    if (desde && hasta && desde.value && hasta.value && desde.value > hasta.value) {
      mostrarError(hasta, 'La fecha final debe ser igual o posterior a la fecha inicial.');
      valido = false;
    }
    return valido;
  }

  function validarFormulario(formulario, enfocarPrimerError) {
    if (!(formulario instanceof HTMLFormElement)) return true;

    let valido = true;
    let primerCampoInvalido = null;

    Array.from(formulario.elements).forEach(function (campo) {
      if (!esCampoEditable(campo)) return;
      if (!validarCampo(campo, true)) {
        valido = false;
        if (!primerCampoInvalido) primerCampoInvalido = campo;
      }
    });

    if (!validarTelefono(formulario)) {
      valido = false;
      if (!primerCampoInvalido) primerCampoInvalido = $(formulario).find('[data-telefono-number]').get(0);
    }
    if (!validarCredenciales(formulario)) {
      valido = false;
      if (!primerCampoInvalido) primerCampoInvalido = $(formulario).find('[name="password_actual"]').get(0);
    }
    if (!validarRangoReporte(formulario)) {
      valido = false;
      if (!primerCampoInvalido) primerCampoInvalido = $(formulario).find('[name="start_date"]').get(0);
    }

    if (!valido && enfocarPrimerError && primerCampoInvalido) {
      primerCampoInvalido.focus({ preventScroll: true });
      primerCampoInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    return valido;
  }

  function esControlValidable(campo) {
    return Boolean(campo && (campo.form || campo.closest('#detalleCabeceraEdicion, #tablaDetallesItems')));
  }

  function validarEdicionCompra(enfocarPrimerError) {
    const proveedor = $('#buscarProveedorEdicion').get(0);
    const factura = $('#edit_factura').get(0);
    const fecha = $('#edit_fecha').get(0);
    const cantidades = $('#tablaDetallesItems [data-purchase-quantity]').toArray();
    const campos = [proveedor, factura, fecha, ...cantidades].filter(Boolean);
    let valido = true;
    let primerCampoInvalido = null;

    campos.forEach(function (campo) {
      if (!validarCampo(campo, true)) {
        valido = false;
        if (!primerCampoInvalido) primerCampoInvalido = campo;
      }
    });

    if (!campos.length) return true;

    if (!valido && enfocarPrimerError && primerCampoInvalido) {
      primerCampoInvalido.focus({ preventScroll: true });
      primerCampoInvalido.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return valido;
  }

  function prepararFormulario(formulario) {
    if (!(formulario instanceof HTMLFormElement)) return;
    const $formulario = $(formulario);
    if ($formulario.data('cgValidationInitialized')) return;

    $formulario.data('cgValidationInitialized', true).prop('noValidate', true);
    $formulario.on('submit.cgValidation', function (evento) {
      if (!validarFormulario(this, true)) {
        evento.preventDefault();
        evento.stopImmediatePropagation();
      }
    });
  }

  function prepararContenedor(contenedor) {
    const $contenedor = $(contenedor);
    $contenedor.filter('form').add($contenedor.find('form')).each(function () {
      prepararFormulario(this);
    });
    inicializarCamposTelefono(contenedor);
  }

  function iniciar() {
    cargarEstilos();
    prepararContenedor(document);

    $(document)
      .off('.cgValidation')
      .on('input.cgValidation', 'input, select, textarea', function () {
        if (esControlValidable(this) && esCampoEditable(this)) validarCampo(this, false);
      })
      .on('change.cgValidation focusout.cgValidation', 'input, select, textarea', function () {
        if (esControlValidable(this) && esCampoEditable(this)) validarCampo(this, true);
      })
      .on('copigaby:contentReady.cgValidation copigaby:refreshed.cgValidation', function (evento) {
        const detail = evento.originalEvent && evento.originalEvent.detail || {};
        prepararContenedor(detail.content || detail.main || document);
      });
  }

  $(iniciar);

  // API pequeña para pruebas y usos puntuales sin acoplar la lógica de los módulos.
  window.CopiGabiValidaciones = Object.freeze({
    validarFormulario: function (formulario) { return validarFormulario(formulario, true); },
    validarEdicionCompra: function () { return validarEdicionCompra(true); }
  });
})(window.jQuery, window, document);