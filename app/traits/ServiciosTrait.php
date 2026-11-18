<?php

namespace App\traits;

/**
 * Trait ServiciosTrait
 * Validaciones del módulo de Servicios.
 */
trait ServiciosTrait
{
    use ValidacionesTrait;

    /**
     * Valida los datos de un servicio.
     */
    protected function validarServicio(array $datos)
    {
        // Campos obligatorios
        if (!$this->campoObligatorioCompleto($datos, 'nombre_servicio')) {
            return ['status' => 'error', 'message' => 'El nombre del servicio es obligatorio'];
        }

        // Precio: estrictamente numérico y mayor a cero (no letras)
        if (
            !$this->campoObligatorioCompleto($datos, 'precio')
            || !$this->numeroFinitoValido($datos['precio'])
            || round((float) $datos['precio'], 2) <= 0
        ) {
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
        $consulta = 'SELECT COUNT(*) FROM servicio WHERE nombre_servicio = ?';
        $parametros = [$nombre];
        if ($idServicio !== null) {
            $consulta .= ' AND codigo_servicio <> ?';
            $parametros[] = $idServicio;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }
}
