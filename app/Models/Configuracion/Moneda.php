<?php

declare(strict_types=1);

namespace App\Models\Configuracion;

class Moneda
{
    private int $codigo_moneda;
    private string $nombre_moneda;
    private float $tasa_cambio;

    public function __construct(int $codigo_moneda, string $nombre_moneda, float $tasa_cambio)
    {
        $this->codigo_moneda = $codigo_moneda;
        $this->nombre_moneda = $nombre_moneda;
        $this->tasa_cambio = $tasa_cambio;
    }

    public function getCodigoMoneda(): int
    {
        return $this->codigo_moneda;
    }

    public function setCodigoMoneda(int $codigo_moneda): void
    {
        $this->codigo_moneda = $codigo_moneda;
    }

    public function getNombreMoneda(): string
    {
        return $this->nombre_moneda;
    }

    public function setNombreMoneda(string $nombre_moneda): void
    {
        $this->nombre_moneda = $nombre_moneda;
    }

    public function getTasaCambio(): float
    {
        return $this->tasa_cambio;
    }

    public function setTasaCambio(float $tasa_cambio): void
    {
        $this->tasa_cambio = $tasa_cambio;
    }
}
