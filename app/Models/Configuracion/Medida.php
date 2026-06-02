<?php

declare(strict_types=1);

namespace App\Models\Configuracion;

class Medida
{
    private int $codigo_medida;
    private string $nombre_medida;

    public function __construct(int $codigo_medida, string $nombre_medida)
    {
        $this->codigo_medida = $codigo_medida;
        $this->nombre_medida = $nombre_medida;
    }

    public function getCodigoMedida(): int
    {
        return $this->codigo_medida;
    }

    public function setCodigoMedida(int $codigo_medida): void
    {
        $this->codigo_medida = $codigo_medida;
    }

    public function getNombreMedida(): string
    {
        return $this->nombre_medida;
    }

    public function setNombreMedida(string $nombre_medida): void
    {
        $this->nombre_medida = $nombre_medida;
    }
}
