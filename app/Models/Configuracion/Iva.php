<?php

declare(strict_types=1);

namespace App\Models\Configuracion;

use App\Models\Core\ConexionBD;

class Iva extends ConexionBD
{
    private int $id_iva;
    private float $porcentaje;
    private bool $principal;

    public function __construct(int $id_iva, float $porcentaje, bool $principal = false)
    {
        parent::__construct();
        $this->id_iva = $id_iva;
        $this->porcentaje = $porcentaje;
        $this->principal = $principal;
    }

    public function getIdIva(): int
    {
        return $this->id_iva;
    }

    public function setIdIva(int $id_iva): void
    {
        $this->id_iva = $id_iva;
    }

    public function getPorcentaje(): float
    {
        return $this->porcentaje;
    }

    public function setPorcentaje(float $porcentaje): void
    {
        $this->porcentaje = $porcentaje;
    }

    public function getPrincipal(): bool
    {
        return $this->principal;
    }

    public function setPrincipal(bool $principal): void
    {
        $this->principal = $principal;
    }
}
