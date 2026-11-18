<?php

namespace App\traits;

/**
 * Trait MetodoPagoTrait
 * Validaciones del módulo de Método de Pago.
 */
trait MetodoPagoTrait
{
    use ValidacionesTrait;

    /**
     * Valida los datos de un método de pago.
     */
    protected function validarMetodoPago(array $datos)
    {
        // Campos obligatorios
        if (!$this->campoObligatorioCompleto($datos, 'nombre_metodo')) {
            return ['status' => 'error', 'message' => 'El nombre del método de pago es obligatorio'];
        }
        if (!$this->campoObligatorioCompleto($datos, 'estado')) {
            return ['status' => 'error', 'message' => 'El estado del método de pago es obligatorio'];
        }

        // Unicidad: no permitir métodos de pago duplicados (ej. EFECTIVO, PAGO MOVIL)
        $idMetodo = isset($datos['codigo_metodo']) ? (int) $datos['codigo_metodo'] : null;
        if ($this->nombreMetodoDuplicado(trim((string) $datos['nombre_metodo']), $idMetodo)) {
            return ['status' => 'error', 'message' => 'Ya existe un método de pago con ese nombre'];
        }

        return true;
    }

    /**
     * Verifica si el método de pago ya existe.
     */
    protected function nombreMetodoDuplicado(string $nombreMetodo, ?int $idMetodo = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM metodo_pago WHERE nombre_metodo = ?';
        $parametros = [$nombreMetodo];
        if ($idMetodo !== null) {
            $consulta .= ' AND codigo_metodo <> ?';
            $parametros[] = $idMetodo;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }
}
