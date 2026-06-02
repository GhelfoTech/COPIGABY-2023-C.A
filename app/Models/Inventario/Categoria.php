<?php

declare(strict_types=1);

namespace App\Models\Inventario;

use App\Models\Core\ConexionBD;

class Categoria extends ConexionBD
{
    private int $codigo_categoria;
    private string $nombre_categoria;
    private bool $estado;

    public function __construct(
        int $codigo_categoria,
        string $nombre_categoria,
        bool $estado = true
    ) {
        parent::__construct();
        $this->codigo_categoria = $codigo_categoria;
        $this->nombre_categoria = $nombre_categoria;
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

    public function getNombreCategoria(): string
    {
        return $this->nombre_categoria;
    }

    public function setNombreCategoria(string $nombre_categoria): void
    {
        $this->nombre_categoria = $nombre_categoria;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): void
    {
        $this->estado = $estado;
    }
}
