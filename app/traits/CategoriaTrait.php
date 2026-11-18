<?php

namespace App\traits;

/**
 * Trait CategoriaTrait
 * Validaciones del módulo de Categoría.
 */
trait CategoriaTrait
{
    use ValidacionesTrait;

    /**
     * Valida los datos de una categoría.
     */
    protected function validarCategoria(array $datos)
    {
        // Campo obligatorio
        if (!$this->campoObligatorioCompleto($datos, 'nombre_categoria')) {
            return ['status' => 'error', 'message' => 'El nombre de la categoría es obligatorio'];
        }

        // Limpieza de espacios
        $nombre = trim((string) $datos['nombre_categoria']);

        // Porcentaje de ganancia obligatorio y numérico
        if (!$this->campoObligatorioCompleto($datos, 'porcentaje_ganancia') || !$this->esNumeroValido($datos['porcentaje_ganancia'])) {
            return ['status' => 'error', 'message' => 'El porcentaje de ganancia es obligatorio y debe ser numérico'];
        }

        // Unicidad: no permitir nombres de categorías duplicadas
        $idCategoria = isset($datos['codigo_categoria']) ? (int) $datos['codigo_categoria'] : null;
        if ($this->nombreCategoriaDuplicado($nombre, $idCategoria)) {
            return ['status' => 'error', 'message' => 'Ya existe una categoría con ese nombre'];
        }

        return true;
    }

    /**
     * Verifica si el nombre de categoría ya existe.
     */
    protected function nombreCategoriaDuplicado(string $nombre, ?int $idCategoria = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM categoria WHERE nombre_categoria = ?';
        $parametros = [$nombre];
        if ($idCategoria !== null) {
            $consulta .= ' AND codigo_categoria <> ?';
            $parametros[] = $idCategoria;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }
}
