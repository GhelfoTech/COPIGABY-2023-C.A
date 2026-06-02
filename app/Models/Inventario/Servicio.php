<?php

declare(strict_types=1);

namespace App\Models\Inventario;

class Servicio
{
    private int $codigo_servicio;
    private string $nombre_servicio;
    private string $descripcion_servicio;
    private float $precio_venta;
    private bool $estado;
    private int $codigo_categoria;

    public function __construct(
        int $codigo_servicio,
        string $nombre_servicio,
        string $descripcion_servicio,
        float $precio_venta,
        int $codigo_categoria,
        bool $estado = true
    ) {
        $this->codigo_servicio = $codigo_servicio;
        $this->nombre_servicio = $nombre_servicio;
        $this->descripcion_servicio = $descripcion_servicio;
        $this->precio_venta = $precio_venta;
        $this->codigo_categoria = $codigo_categoria;
        $this->estado = $estado;
    }

    public function getCodigoServicio(): int
    {
        return $this->codigo_servicio;
    }

    public function setCodigoServicio(int $codigo_servicio): void
    {
        $this->codigo_servicio = $codigo_servicio;
    }

    public function getNombreServicio(): string
    {
        return $this->nombre_servicio;
    }

    public function setNombreServicio(string $nombre_servicio): void
    {
        $this->nombre_servicio = $nombre_servicio;
    }

    public function getDescripcionServicio(): string
    {
        return $this->descripcion_servicio;
    }

    public function setDescripcionServicio(string $descripcion_servicio): void
    {
        $this->descripcion_servicio = $descripcion_servicio;
    }

    public function getPrecioVenta(): float
    {
        return $this->precio_venta;
    }

    public function setPrecioVenta(float $precio_venta): void
    {
        $this->precio_venta = $precio_venta;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): void
    {
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
}
