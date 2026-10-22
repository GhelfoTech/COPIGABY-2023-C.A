<?php

namespace App\traits;

/**
 * Trait MonedaTrait
 * Validaciones del módulo de Moneda.
 */
trait MonedaTrait
{
    /**
     * Valida los datos de una moneda.
     */
    protected function validarMoneda(array $datos)
    {
        // Campos obligatorios
        foreach (['nombre_moneda', 'simbolo', 'tasa_cambio', 'estado'] as $campo) {
            if (!isset($datos[$campo]) || trim((string) $datos[$campo]) === '') {
                return ['status' => 'error', 'message' => 'Nombre, símbolo, tasa asociada y estado son obligatorios'];
            }
        }

        // La tasa asociada debe ser mayor a cero
        if (!is_numeric($datos['tasa_cambio']) || (float) $datos['tasa_cambio'] <= 0) {
            return ['status' => 'error', 'message' => 'La tasa de cambio debe ser un número mayor a cero'];
        }

        // Unicidad: evitar monedas repetidas
        $idMoneda = isset($datos['codigo_moneda']) ? (int) $datos['codigo_moneda'] : null;
        if ($this->nombreMonedaDuplicado(trim((string) $datos['nombre_moneda']), $idMoneda)) {
            return ['status' => 'error', 'message' => 'Ya existe una moneda con ese nombre'];
        }

        // Solo una moneda principal puede estar marcada como activa (activa = 1)
        if (isset($datos['activa']) && (int) $datos['activa'] === 1 && $this->existeOtraMonedaActiva($idMoneda)) {
            return ['status' => 'error', 'message' => 'Ya existe otra moneda marcada como principal activa'];
        }

        return true;
    }

    /**
     * Verifica si el nombre de la moneda ya existe.
     */
    protected function nombreMonedaDuplicado(string $nombreMoneda, ?int $idMoneda = null): bool
    {
        try {
            if ($idMoneda !== null) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM moneda WHERE nombre_moneda = ? AND codigo_moneda <> ?'
                );
                $stmt->execute([$nombreMoneda, $idMoneda]);
            } else {
                $stmt = $this->conex->prepare('SELECT COUNT(*) FROM moneda WHERE nombre_moneda = ?');
                $stmt->execute([$nombreMoneda]);
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Verifica si existe otra moneda con activa = 1 (si la columna existe).
     */
    protected function existeOtraMonedaActiva(?int $idMoneda = null): bool
    {
        try {
            $stmt = $this->conex->prepare("SHOW COLUMNS FROM moneda LIKE 'activa'");
            $stmt->execute();
            if ($stmt->rowCount() === 0) {
                return false; // La columna no existe, no se puede validar
            }

            if ($idMoneda !== null) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM moneda WHERE activa = 1 AND codigo_moneda <> ?'
                );
                $stmt->execute([$idMoneda]);
            } else {
                $stmt = $this->conex->prepare('SELECT COUNT(*) FROM moneda WHERE activa = 1');
                $stmt->execute();
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
