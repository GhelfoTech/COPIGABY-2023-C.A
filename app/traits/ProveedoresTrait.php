<?php

namespace App\traits;

/**
 * Trait ProveedoresTrait
 * Validaciones del módulo de Proveedores.
 */
trait ProveedoresTrait
{
    use ValidacionesTrait;

    /**
     * Valida los datos de un proveedor.
     */
    protected function validarProveedor(array $datos)
    {
        // Campos obligatorios
        if (!$this->camposObligatoriosCompletos($datos, ['rif_proveedor', 'razon_social', 'telefono', 'correo', 'estado'])) {
            return ['status' => 'error', 'message' => 'Todos los campos obligatorios del proveedor deben ser completados'];
        }

        // Unicidad estricta del RIF
        $idProveedor = isset($datos['codigo_proveedor_actual']) ? (int) $datos['codigo_proveedor_actual'] : null;
        if ($this->rifProveedorDuplicado(trim((string) $datos['rif_proveedor']), $idProveedor)) {
            return ['status' => 'error', 'message' => 'El RIF ya está registrado en el sistema'];
        }

        // Teléfono: formato numérico
        if (!$this->telefonoNacionalValido($datos['telefono'])) {
            return ['status' => 'error', 'message' => 'El teléfono debe tener 11 dígitos numéricos'];
        }

        // Correo: formato válido y dominio terminado en .com
        $correo = trim((string) $datos['correo']);
        if (!$this->correoComValido($correo)) {
            return ['status' => 'error', 'message' => 'El correo electrónico debe tener un formato válido y terminar en .com'];
        }

        // Evitar duplicidad de correos corporativos
        if ($this->correoProveedorDuplicado($correo, $idProveedor)) {
            return ['status' => 'error', 'message' => 'El correo ya está registrado para otro proveedor'];
        }

        return true;
    }

    /**
     * Unicidad estricta del RIF (excluyendo un id opcional en actualización).
     */
    protected function rifProveedorDuplicado(string $rif, ?int $idProveedor = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM proveedor WHERE rif_proveedor = ?';
        $parametros = [$rif];
        if ($idProveedor !== null) {
            $consulta .= ' AND codigo_proveedor <> ?';
            $parametros[] = $idProveedor;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }

    /**
     * Evita la duplicidad de correos corporativos.
     */
    protected function correoProveedorDuplicado(string $correo, ?int $idProveedor = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM proveedor WHERE correo = ?';
        $parametros = [$correo];
        if ($idProveedor !== null) {
            $consulta .= ' AND codigo_proveedor <> ?';
            $parametros[] = $idProveedor;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }
}
