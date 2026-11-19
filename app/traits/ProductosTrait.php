<?php

namespace App\traits;

/**
 * Trait ProductosTrait
 * Validaciones del módulo de Productos-Insumo.
 */
trait ProductosTrait
{
    use ValidacionesTrait;

    /**
     * Valida los datos de un producto/insumo.
     */
    protected function validarProducto(array $datos)
    {
        // Campos obligatorios
        if (!$this->camposObligatoriosCompletos($datos, ['nombre_producto', 'codigo_categoria', 'stock_actual', 'stock_minimo'])) {
            return ['status' => 'error', 'message' => 'Los campos código, nombre, categoría, stock actual y stock mínimo son obligatorios'];
        }

        $descripcion = $datos['descripcion'] ?? '';
        if (!is_scalar($descripcion) || mb_strlen((string) $descripcion, 'UTF-8') > 50) {
            return ['status' => 'error', 'message' => 'La descripción del producto no puede superar los 50 caracteres'];
        }

        // Stock actual y mínimo no pueden ser negativos (enteros o decimales)
        $stockActual = (float) $datos['stock_actual'];
        $stockMinimo = (float) $datos['stock_minimo'];

        if ($stockActual < 0) {
            return ['status' => 'error', 'message' => 'El stock actual no puede ser negativo'];
        }
        if ($stockMinimo < 0) {
            return ['status' => 'error', 'message' => 'El stock mínimo no puede ser negativo'];
        }

        // No permitir nombres de productos duplicados dentro de la misma categoría
        $idProducto = isset($datos['codigo_producto']) ? (int) $datos['codigo_producto'] : null;
        if ($this->nombreProductoDuplicado(trim($datos['nombre_producto']), (int) $datos['codigo_categoria'], $idProducto)) {
            return ['status' => 'error', 'message' => 'Ya existe un producto con ese nombre en la misma categoría'];
        }

        return true;
    }

    /**
     * Verifica si el nombre del producto ya existe dentro de la misma categoría.
     */
    protected function nombreProductoDuplicado(string $nombre, int $codigoCategoria, ?int $idProducto = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM producto_insumo
                     WHERE nombre_producto = ? AND codigo_categoria = ?';
        $parametros = [$nombre, $codigoCategoria];
        if ($idProducto !== null) {
            $consulta .= ' AND codigo_producto <> ?';
            $parametros[] = $idProducto;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }
}
