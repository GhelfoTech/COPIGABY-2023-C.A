<?php

declare(strict_types=1);

namespace App\Models\Configuracion;

class MetodoPago
{
    private int $codigo_metodo;
    private string $nombre_metodo;

    public function __construct(int $codigo_metodo, string $nombre_metodo)
    {
        $this->codigo_metodo = $codigo_metodo;
        $this->nombre_metodo = $nombre_metodo;
    }

    public function getCodigoMetodo(): int
    {
        return $this->codigo_metodo;
    }

    public function setCodigoMetodo(int $codigo_metodo): void
    {
        $this->codigo_metodo = $codigo_metodo;
    }

    public function getNombreMetodo(): string
    {
        return $this->nombre_metodo;
    }

    public function setNombreMetodo(string $nombre_metodo): void
    {
        $this->nombre_metodo = $nombre_metodo;
    }
}
