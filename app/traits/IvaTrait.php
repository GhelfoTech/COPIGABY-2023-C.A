<?php

namespace App\traits;

/**
 * Trait IvaTrait
 * Validaciones del módulo de IVA.
 */
trait IvaTrait
{
    use ValidacionesTrait;

    /**
     * Valida los datos de un registro de IVA.
     */
    protected function validarIva(array $datos)
    {
        // Campos obligatorios
        if (!$this->campoObligatorioCompleto($datos, 'porcentaje_iva')) {
            return ['status' => 'error', 'message' => 'El porcentaje de IVA es obligatorio'];
        }
        if (!$this->campoObligatorioCompleto($datos, 'estado')) {
            return ['status' => 'error', 'message' => 'El estado es obligatorio'];
        }

        // Validación numérica: porcentaje entre 0.00 y 100.00
        if (!$this->esNumeroValido($datos['porcentaje_iva'])) {
            return ['status' => 'error', 'message' => 'El porcentaje de IVA debe ser numérico'];
        }

        $porcentaje = (float) $datos['porcentaje_iva'];
        if ($porcentaje <= 0.00 || $porcentaje > 100.00) {
            return ['status' => 'error', 'message' => 'El porcentaje de IVA debe ser mayor que cero y hasta 100.00'];
        }

        return true;
    }
}
