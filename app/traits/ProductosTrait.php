<?php

namespace App\traits;

/**
 * Trait ProductosTrait
 * Validaciones del módulo de Productos-Insumo.
 */
trait ProductosTrait
{
    /**
     * Valida los datos de un producto/insumo.
     */
    protected function validarProducto(array $datos)
    {
        // Campos obligatorios
        foreach (['nombre_producto', 'codigo_categoria', 'stock_actual', 'stock_minimo'] as $campo) {
            if (!isset($datos[$campo]) || trim((string) $datos[$campo]) === '') {
                return ['status' => 'error', 'message' => 'Los campos código, nombre, categoría, stock actual y stock mínimo son obligatorios'];
            }
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
        try {
            if ($idProducto !== null) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM producto_insumo
                     WHERE nombre_producto = ? AND codigo_categoria = ? AND codigo_producto <> ?'
                );
                $stmt->execute([$nombre, $codigoCategoria, $idProducto]);
            } else {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM producto_insumo
                     WHERE nombre_producto = ? AND codigo_categoria = ?'
                );
                $stmt->execute([$nombre, $codigoCategoria]);
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
