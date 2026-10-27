<?php

namespace App\traits;

/**
 * Trait MetodoPagoTrait
 * Validaciones del módulo de Método de Pago.
 */
trait MetodoPagoTrait
{
    /**
     * Valida los datos de un método de pago.
     */
    protected function validarMetodoPago(array $datos)
    {
        // Campos obligatorios
        if (!isset($datos['nombre_metodo']) || trim((string) $datos['nombre_metodo']) === '') {
            return ['status' => 'error', 'message' => 'El nombre del método de pago es obligatorio'];
        }
        if (!isset($datos['estado']) || trim((string) $datos['estado']) === '') {
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
        try {
            if ($idMetodo !== null) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM metodo_pago WHERE nombre_metodo = ? AND codigo_metodo <> ?'
                );
                $stmt->execute([$nombreMetodo, $idMetodo]);
            } else {
                $stmt = $this->conex->prepare('SELECT COUNT(*) FROM metodo_pago WHERE nombre_metodo = ?');
                $stmt->execute([$nombreMetodo]);
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
