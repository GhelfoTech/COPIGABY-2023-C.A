<?php

namespace App\traits;

/**
 * Trait IvaTrait
 * Validaciones del módulo de IVA.
 */
trait IvaTrait
{
    /**
     * Valida los datos de un registro de IVA.
     */
    protected function validarIva(array $datos)
    {
        // Campos obligatorios
        if (!isset($datos['porcentaje_iva']) || trim((string) $datos['porcentaje_iva']) === '') {
            return ['status' => 'error', 'message' => 'El porcentaje de IVA es obligatorio'];
        }
        if (!isset($datos['estado']) || trim((string) $datos['estado']) === '') {
            return ['status' => 'error', 'message' => 'El estado es obligatorio'];
        }

        // Validación numérica: porcentaje entre 0.00 y 100.00
        if (!is_numeric($datos['porcentaje_iva'])) {
            return ['status' => 'error', 'message' => 'El porcentaje de IVA debe ser numérico'];
        }

        $porcentaje = (float) $datos['porcentaje_iva'];
        if ($porcentaje < 0.00 || $porcentaje > 100.00) {
            return ['status' => 'error', 'message' => 'El porcentaje de IVA debe estar entre 0.00 y 100.00'];
        }

        return true;
    }
}
