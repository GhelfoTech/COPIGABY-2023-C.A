<?php

declare(strict_types=1);

namespace App\Models\Compras;

use App\Models\Core\ConexionBD;

class Compra extends ConexionBD
{
    private string $codigo_compra;
    private string $fecha_compra;
    private string $numero_factura;
    private float $monto_total;
    private bool $estado;
    private int $codigo_proveedor;
    private string $codigo_usuario;

    public function __construct(
        string $codigo_compra,
        string $fecha_compra,
        string $numero_factura,
        float $monto_total,
        int $codigo_proveedor,
        string $codigo_usuario,
        bool $estado = true
    ) {
        parent::__construct();
        $this->codigo_compra = $codigo_compra;
        $this->fecha_compra = $fecha_compra;
        $this->numero_factura = $numero_factura;
        $this->monto_total = $monto_total;
        $this->codigo_proveedor = $codigo_proveedor;
        $this->codigo_usuario = $codigo_usuario;
        $this->estado = $estado;
    }

    public function calcularTotal(array $detalles): float
    {
        $total = 0.0;

        foreach ($detalles as $detalle) {
            if ($detalle instanceof DetalleCompra) {
                $total += $detalle->getCantidad() * $detalle->getCostoUnitario();
            }
        }

        $this->monto_total = $total;

        return $total;
    }

    public function getCodigoCompra(): string
    {
        return $this->codigo_compra;
    }

    public function setCodigoCompra(string $codigo_compra): void
    {
        $this->codigo_compra = $codigo_compra;
    }

    public function getFechaCompra(): string
    {
        return $this->fecha_compra;
    }

    public function setFechaCompra(string $fecha_compra): void
    {
        $this->fecha_compra = $fecha_compra;
    }

    public function getNumeroFactura(): string
    {
        return $this->numero_factura;
    }

    public function setNumeroFactura(string $numero_factura): void
    {
        $this->numero_factura = $numero_factura;
    }

    public function getMontoTotal(): float
    {
        return $this->monto_total;
    }

    public function setMontoTotal(float $monto_total): void
    {
        $this->monto_total = $monto_total;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): void
    {
        $this->estado = $estado;
    }

    public function getCodigoProveedor(): int
    {
        return $this->codigo_proveedor;
    }

    public function setCodigoProveedor(int $codigo_proveedor): void
    {
        $this->codigo_proveedor = $codigo_proveedor;
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
