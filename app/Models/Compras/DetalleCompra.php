<?php

declare(strict_types=1);

namespace App\Models\Compras;

use App\Models\Core\ConexionBD;

class DetalleCompra extends ConexionBD
{
    private string $codigo_detalle_compra;
    private int $cantidad;
    private float $costo_unitario;
    private string $codigo_compra;
    private int $codigo_producto;

    public function __construct(
        string $codigo_detalle_compra,
        int $cantidad,
        float $costo_unitario,
        string $codigo_compra,
        int $codigo_producto
    ) {
        parent::__construct();
        $this->codigo_detalle_compra = $codigo_detalle_compra;
        $this->cantidad = $cantidad;
        $this->costo_unitario = $costo_unitario;
        $this->codigo_compra = $codigo_compra;
        $this->codigo_producto = $codigo_producto;
    }

    public function getCodigoDetalleCompra(): string
    {
        return $this->codigo_detalle_compra;
    }

    public function setCodigoDetalleCompra(string $codigo_detalle_compra): void
    {
        $this->codigo_detalle_compra = $codigo_detalle_compra;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): void
    {
        $this->cantidad = $cantidad;
    }

    public function getCostoUnitario(): float
    {
        return $this->costo_unitario;
    }

    public function setCostoUnitario(float $costo_unitario): void
    {
        $this->costo_unitario = $costo_unitario;
    }

    public function getCodigoCompra(): string
    {
        return $this->codigo_compra;
    }

    public function setCodigoCompra(string $codigo_compra): void
    {
        $this->codigo_compra = $codigo_compra;
    }

    public function getCodigoProducto(): int
    {
        return $this->codigo_producto;
    }

    public function setCodigoProducto(int $codigo_producto): void
    {
        $this->codigo_producto = $codigo_producto;
    }
}
