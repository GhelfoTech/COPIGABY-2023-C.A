<?php

declare(strict_types=1);

namespace App\Models\Ventas;

class Pedido
{
    private string $codigo_venta;
    private string $fecha_venta;
    private float $total_venta;
    private int $codigo_cliente;
    private string $codigo_usuario;

    public function __construct(
        string $codigo_venta,
        string $fecha_venta,
        float $total_venta,
        int $codigo_cliente,
        string $codigo_usuario
    ) {
        $this->codigo_venta = $codigo_venta;
        $this->fecha_venta = $fecha_venta;
        $this->total_venta = $total_venta;
        $this->codigo_cliente = $codigo_cliente;
        $this->codigo_usuario = $codigo_usuario;
    }

    public function getCodigoVenta(): string
    {
        return $this->codigo_venta;
    }

    public function setCodigoVenta(string $codigo_venta): void
    {
        $this->codigo_venta = $codigo_venta;
    }

    public function getFechaVenta(): string
    {
        return $this->fecha_venta;
    }

    public function setFechaVenta(string $fecha_venta): void
    {
        $this->fecha_venta = $fecha_venta;
    }

    public function getTotalVenta(): float
    {
        return $this->total_venta;
    }

    public function setTotalVenta(float $total_venta): void
    {
        $this->total_venta = $total_venta;
    }

    public function getCodigoCliente(): int
    {
        return $this->codigo_cliente;
    }

    public function setCodigoCliente(int $codigo_cliente): void
    {
        $this->codigo_cliente = $codigo_cliente;
    }

    public function getCodigoUsuario(): string
    {
        return $this->codigo_usuario;
    }

    public function setCodigoUsuario(string $codigo_usuario): void
    {
        $this->codigo_usuario = $codigo_usuario;
    }
}
