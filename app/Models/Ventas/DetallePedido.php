<?php

declare(strict_types=1);

namespace App\Models\Ventas;

use App\Models\Core\ConexionBD;

class DetallePedido extends ConexionBD
{
    private int $cantidad;
    private float $precio_unitario;
    private float $subtotal;
    private string $codigo_venta;
    private ?int $codigo_producto;
    private ?int $codigo_servicio;

    public function __construct(
        int $cantidad,
        float $precio_unitario,
        string $codigo_venta,
        ?int $codigo_producto = null,
        ?int $codigo_servicio = null,
        float $subtotal = 0.0
    ) {
        parent::__construct();
        $this->cantidad = $cantidad;
        $this->precio_unitario = $precio_unitario;
        $this->codigo_venta = $codigo_venta;
        $this->codigo_producto = $codigo_producto;
        $this->codigo_servicio = $codigo_servicio;
        $this->subtotal = $subtotal;
    }

    public function calcularSubtotal(): float
    {
        $this->subtotal = $this->cantidad * $this->precio_unitario;

        return $this->subtotal;
    }

    public function getCantidad(): int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): void
    {
        $this->cantidad = $cantidad;
    }

    public function getPrecioUnitario(): float
    {
        return $this->precio_unitario;
    }

    public function setPrecioUnitario(float $precio_unitario): void
    {
        $this->precio_unitario = $precio_unitario;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    public function getCodigoVenta(): string
    {
        return $this->codigo_venta;
    }

    public function setCodigoVenta(string $codigo_venta): void
    {
        $this->codigo_venta = $codigo_venta;
    }

    public function getCodigoProducto(): ?int
    {
        return $this->codigo_producto;
    }

    public function setCodigoProducto(?int $codigo_producto): void
    {
        $this->codigo_producto = $codigo_producto;
    }

    public function getCodigoServicio(): ?int
    {
        return $this->codigo_servicio;
    }

    public function setCodigoServicio(?int $codigo_servicio): void
    {
        $this->codigo_servicio = $codigo_servicio;
    }
}
