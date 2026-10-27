<?php

namespace App\traits;

/**
 * Trait ServiciosTrait
 * Validaciones del módulo de Servicios.
 */
trait ServiciosTrait
{
    /**
     * Valida los datos de un servicio.
     */
    protected function validarServicio(array $datos)
    {
        // Campos obligatorios
        if (!isset($datos['nombre_servicio']) || trim((string) $datos['nombre_servicio']) === '') {
            return ['status' => 'error', 'message' => 'El nombre del servicio es obligatorio'];
        }

        // Precio: estrictamente numérico y mayor a cero (no letras)
        if (!isset($datos['precio']) || !is_numeric($datos['precio']) || (float) $datos['precio'] <= 0) {
            return ['status' => 'error', 'message' => 'El precio debe ser un valor numérico mayor a cero'];
        }

        // Unicidad: evitar nombres de servicios duplicados
        $idServicio = isset($datos['codigo_servicio']) ? (int) $datos['codigo_servicio'] : null;
        if ($this->nombreServicioDuplicado(trim((string) $datos['nombre_servicio']), $idServicio)) {
            return ['status' => 'error', 'message' => 'Ya existe un servicio con ese nombre'];
        }

        return true;
    }

    /**
     * Verifica si el nombre del servicio ya existe.
     */
    protected function nombreServicioDuplicado(string $nombre, ?int $idServicio = null): bool
    {
        try {
            if ($idServicio !== null) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM servicio WHERE nombre_servicio = ? AND codigo_servicio <> ?'
                );
                $stmt->execute([$nombre, $idServicio]);
            } else {
                $stmt = $this->conex->prepare('SELECT COUNT(*) FROM servicio WHERE nombre_servicio = ?');
                $stmt->execute([$nombre]);
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
