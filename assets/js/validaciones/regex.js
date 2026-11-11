(function (window) {
  'use strict';

  window.CopiGabiRegex = Object.freeze({
    // Nombres personales: letras españolas y espacios.
    letras: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/,

    // Texto general: letras, números y puntuación habitual en nombres, direcciones y descripciones.
    texto: /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ0-9\s.,;:¡!¿?()'"&_#%+\-°ºª$€²³@/:\\]+$/,

    // Campos que deben contener únicamente dígitos.
    numeros: /^[0-9]+$/,

    // Cédula de cliente: regla alineada con ClientesTrait (6 a 9 dígitos).
    cedula: /^[0-9]{6,9}$/,

    // Teléfono nacional completo guardado por el componente telefónico (11 dígitos).
    telefono: /^[0-9]{11}$/,

    // Importes y porcentajes: punto decimal y hasta dos cifras decimales.
    decimal: /^[0-9]+(?:\.[0-9]{1,2})?$/,

    // Formato general de correo; los módulos actuales además exigen terminación .com.
    correo: /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/
  });
})(window);
