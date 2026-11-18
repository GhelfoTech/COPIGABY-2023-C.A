<?php

namespace App\traits;

/**
 * Trait MonedaTrait
 * Validaciones del módulo de Moneda.
 */
trait MonedaTrait
{
    use ValidacionesTrait;

    /**
     * Valida los datos de una moneda.
     */
    protected function validarMoneda(array $datos)
    {
        // Campos obligatorios
        if (!$this->camposObligatoriosCompletos($datos, ['nombre_moneda', 'simbolo', 'tasa_cambio', 'estado'])) {
            return ['status' => 'error', 'message' => 'Nombre, símbolo, tasa asociada y estado son obligatorios'];
        }

        // La tasa asociada debe ser mayor a cero
        if (!$this->esNumeroValido($datos['tasa_cambio']) || (float) $datos['tasa_cambio'] <= 0) {
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
        $consulta = 'SELECT COUNT(*) FROM moneda WHERE nombre_moneda = ?';
        $parametros = [$nombreMoneda];
        if ($idMoneda !== null) {
            $consulta .= ' AND codigo_moneda <> ?';
            $parametros[] = $idMoneda;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
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

            $consulta = 'SELECT COUNT(*) FROM moneda WHERE activa = 1';
            $parametros = [];
            if ($idMoneda !== null) {
                $consulta .= ' AND codigo_moneda <> ?';
                $parametros[] = $idMoneda;
            }
            return $this->registroDuplicadoPorConsulta($consulta, $parametros);
        } catch (\PDOException $e) {
            return false;
        }
    }
}
