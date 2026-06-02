<?php

declare(strict_types=1);

namespace App\Models\Configuracion;

class Pago
{
    private int $codigo_pago;
    private float $monto;
    private string $fecha;
    private string $numero_referencia;
    private string $codigo_venta;
    private int $codigo_metodo;
    private int $codigo_moneda;

    public function __construct(
        int $codigo_pago,
        float $monto,
        string $fecha,
        string $numero_referencia,
        string $codigo_venta,
        int $codigo_metodo,
        int $codigo_moneda
    ) {
        $this->codigo_pago = $codigo_pago;
        $this->monto = $monto;
        $this->fecha = $fecha;
        $this->numero_referencia = $numero_referencia;
        $this->codigo_venta = $codigo_venta;
        $this->codigo_metodo = $codigo_metodo;
        $this->codigo_moneda = $codigo_moneda;
    }

    public function getCodigoPago(): int
    {
        return $this->codigo_pago;
    }

    public function setCodigoPago(int $codigo_pago): void
    {
        $this->codigo_pago = $codigo_pago;
    }

    public function getMonto(): float
    {
        return $this->monto;
    }

    public function setMonto(float $monto): void
    {
        $this->monto = $monto;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function setFecha(string $fecha): void
    {
        $this->fecha = $fecha;
    }

    public function getNumeroReferencia(): string
    {
        return $this->numero_referencia;
    }

    public function setNumeroReferencia(string $numero_referencia): void
    {
        $this->numero_referencia = $numero_referencia;
    }

    public function getCodigoVenta(): string
    {
        return $this->codigo_venta;
    }

    public function setCodigoVenta(string $codigo_venta): void
    {
        $this->codigo_venta = $codigo_venta;
    }

    public function getCodigoMetodo(): int
    {
        return $this->codigo_metodo;
    }

    public function setCodigoMetodo(int $codigo_metodo): void
    {
        $this->codigo_metodo = $codigo_metodo;
    }

    public function getCodigoMoneda(): int
    {
        return $this->codigo_moneda;
    }

    public function setCodigoMoneda(int $codigo_moneda): void
    {
        $this->codigo_moneda = $codigo_moneda;
    }
}
