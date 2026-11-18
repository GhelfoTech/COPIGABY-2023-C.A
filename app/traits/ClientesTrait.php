<?php

namespace App\traits;

/**
 * Trait ClientesTrait
 * Validaciones del módulo de Clientes.
 */
trait ClientesTrait
{
    use ValidacionesTrait;

    /**
     * Valida los datos completos de un cliente.
     */
    protected function validarCliente(array $datos)
    {
        // Campos obligatorios
        if (!$this->camposObligatoriosCompletos($datos, ['cedula_cliente', 'nombre', 'telefono', 'direccion', 'estado'])) {
            return ['status' => 'error', 'message' => 'Todos los campos obligatorios del cliente deben ser completados'];
        }

        // Cédula: solo números, sin letras ni espacios
        $validacionCedula = $this->validarCedulaCliente($datos['cedula_cliente']);
        if ($validacionCedula !== true) {
            return $validacionCedula;
        }

        // Nombre: solo letras y espacios
        if (!preg_match('/^[A-Za-zÁÉÍÓÚÑáéíóúñ\s]+$/', trim($datos['nombre']))) {
            return ['status' => 'error', 'message' => 'El nombre solo puede contener letras y espacios'];
        }

        // Teléfono: solo números, formato nacional válido (11 dígitos, ej. 04245555555)
        $validacionTelefono = $this->validarTelefonoCliente($datos['telefono']);
        if ($validacionTelefono !== true) {
            return $validacionTelefono;
        }

        $idActual = isset($datos['cedula_cliente_actual']) ? $datos['cedula_cliente_actual'] : null;
        if ($this->cedulaClienteDuplicada(trim((string) $datos['cedula_cliente']), $idActual)) {
            return ['status' => 'error', 'message' => 'La cédula ya está registrada en el sistema'];
        }

        // Correo opcional: formato válido y dominio terminado en .com
        $correo = trim((string) ($datos['correo'] ?? ''));
        if ($correo !== '' && !$this->correoComValido($correo)) {
            return ['status' => 'error', 'message' => 'El correo electrónico debe tener un formato válido y terminar en .com'];
        }

        return true;
    }

    /**
     * Cédula: solo números, sin letras ni espacios, longitud válida.
     */
    protected function validarCedulaCliente($cedula)
    {
        $cedula = trim((string) $cedula);
        if (!$this->soloDigitos($cedula)) {
            return ['status' => 'error', 'message' => 'La cédula solo debe contener números, sin letras ni espacios'];
        }
        if (strlen($cedula) < 6 || strlen($cedula) > 9) {
            return ['status' => 'error', 'message' => 'La cédula debe tener entre 6 y 9 dígitos'];
        }
        return true;
    }

    /**
     * Teléfono: solo números, formato nacional válido (11 dígitos).
     */
    protected function validarTelefonoCliente($telefono)
    {
        if (!$this->telefonoNacionalValido($telefono)) {
            return ['status' => 'error', 'message' => 'El teléfono debe tener 11 dígitos numéricos (ej. 04245555555)'];
        }
        return true;
    }

    /**
     * Unicidad estricta de cédula (excluyendo un id opcional en actualización).
     */
    protected function cedulaClienteDuplicada(string $cedula, $idActual = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM cliente WHERE cedula_cliente = ?';
        $parametros = [$cedula];
        if ($idActual !== null) {
            $consulta .= ' AND cedula_cliente <> ?';
            $parametros[] = $idActual;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }
}
