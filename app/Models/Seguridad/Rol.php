<?php

declare(strict_types=1);

namespace App\Models\Seguridad;

class Rol
{
    private int $codigo_rol;
    private string $nombre_rol;
    private string $descripcion;
    private bool $estado;

    public function __construct(
        int $codigo_rol,
        string $nombre_rol,
        string $descripcion,
        bool $estado = true
    ) {
        $this->codigo_rol = $codigo_rol;
        $this->nombre_rol = $nombre_rol;
        $this->descripcion = $descripcion;
        $this->estado = $estado;
    }

    public function getCodigoRol(): int
    {
        return $this->codigo_rol;
    }

    public function setCodigoRol(int $codigo_rol): void
    {
        $this->codigo_rol = $codigo_rol;
    }

    public function getNombreRol(): string
    {
        return $this->nombre_rol;
    }

    public function setNombreRol(string $nombre_rol): void
    {
        $this->nombre_rol = $nombre_rol;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
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
