<?php

declare(strict_types=1);

namespace App\Models\Inventario;

class Producto
{
    private int $codigo_producto;
    private string $nombre_producto;
    private string $descripcion_producto;
    private int $stock_actual;
    private int $stock_minimo;
    private float $precio_costo;
    private float $precio_venta;
    private bool $estado;
    private int $codigo_categoria;
    private int $codigo_medida;

    public function __construct(
        int $codigo_producto,
        string $nombre_producto,
        string $descripcion_producto,
        int $stock_actual,
        int $stock_minimo,
        float $precio_costo,
        float $precio_venta,
        int $codigo_categoria,
        int $codigo_medida,
        bool $estado = true
    ) {
        $this->codigo_producto = $codigo_producto;
        $this->nombre_producto = $nombre_producto;
        $this->descripcion_producto = $descripcion_producto;
        $this->stock_actual = $stock_actual;
        $this->stock_minimo = $stock_minimo;
        $this->precio_costo = $precio_costo;
        $this->precio_venta = $precio_venta;
        $this->codigo_categoria = $codigo_categoria;
        $this->codigo_medida = $codigo_medida;
        $this->estado = $estado;
    }

    public function validarStock(int $cantidad): bool
    {
        return $this->estado && $cantidad > 0 && $this->stock_actual >= $cantidad;
    }

    public function descontarStock(int $cantidad): bool
    {
        if (!$this->validarStock($cantidad)) {
            return false;
        }

        $this->stock_actual -= $cantidad;

        return true;
    }

    public function agregarStock(int $cantidad): void
    {
        if ($cantidad > 0) {
            $this->stock_actual += $cantidad;
        }
    }

    public function getCodigoProducto(): int
    {
        return $this->codigo_producto;
    }

    public function setCodigoProducto(int $codigo_producto): void
    {
        $this->codigo_producto = $codigo_producto;
    }

    public function getNombreProducto(): string
    {
        return $this->nombre_producto;
    }

    public function setNombreProducto(string $nombre_producto): void
    {
        $this->nombre_producto = $nombre_producto;
    }

    public function getDescripcionProducto(): string
    {
        return $this->descripcion_producto;
    }

    public function setDescripcionProducto(string $descripcion_producto): void
    {
        $this->descripcion_producto = $descripcion_producto;
    }

    public function getStockActual(): int
    {
        return $this->stock_actual;
    }

    public function setStockActual(int $stock_actual): void
    {
        $this->stock_actual = $stock_actual;
    }

    public function getStockMinimo(): int
    {
        return $this->stock_minimo;
    }

    public function setStockMinimo(int $stock_minimo): void
    {
        $this->stock_minimo = $stock_minimo;
    }

    public function getPrecioCosto(): float
    {
        return $this->precio_costo;
    }

    public function setPrecioCosto(float $precio_costo): void
    {
        $this->precio_costo = $precio_costo;
    }

    public function getPrecioVenta(): float
    {
        return $this->precio_venta;
    }

    public function setPrecioVenta(float $precio_venta): void
    {
        $this->precio_venta = $precio_venta;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): void
    {
        $this->estado = $estado;
    }

    public function getCodigoCategoria(): int
    {
        return $this->codigo_categoria;
    }

    public function setCodigoCategoria(int $codigo_categoria): void
    {
        $this->codigo_categoria = $codigo_categoria;
    }

    public function getCodigoMedida(): int
    {
        return $this->codigo_medida;
    }

    public function setCodigoMedida(int $codigo_medida): void
    {
        $this->codigo_medida = $codigo_medida;
    }
}
