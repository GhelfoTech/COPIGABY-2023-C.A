<?php

declare(strict_types=1);

namespace App\Models\Compras;

class Proveedor
{
    private int $codigo_proveedor;
    private string $proveedor;
    private string $razon_social;
    private string $telefono_proveedor;
    private string $correo_proveedor;
    private string $tipo_rubro;
    private string $direccion_proveedor;
    private bool $estado;

    public function __construct(
        int $codigo_proveedor,
        string $proveedor,
        string $razon_social,
        string $telefono_proveedor,
        string $correo_proveedor,
        string $tipo_rubro,
        string $direccion_proveedor,
        bool $estado = true
    ) {
        $this->codigo_proveedor = $codigo_proveedor;
        $this->proveedor = $proveedor;
        $this->razon_social = $razon_social;
        $this->telefono_proveedor = $telefono_proveedor;
        $this->correo_proveedor = $correo_proveedor;
        $this->tipo_rubro = $tipo_rubro;
        $this->direccion_proveedor = $direccion_proveedor;
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

    public function getProveedor(): string
    {
        return $this->proveedor;
    }

    public function setProveedor(string $proveedor): void
    {
        $this->proveedor = $proveedor;
    }

    public function getRazonSocial(): string
    {
        return $this->razon_social;
    }

    public function setRazonSocial(string $razon_social): void
    {
        $this->razon_social = $razon_social;
    }

    public function getTelefonoProveedor(): string
    {
        return $this->telefono_proveedor;
    }

    public function setTelefonoProveedor(string $telefono_proveedor): void
    {
        $this->telefono_proveedor = $telefono_proveedor;
    }

    public function getCorreoProveedor(): string
    {
        return $this->correo_proveedor;
    }

    public function setCorreoProveedor(string $correo_proveedor): void
    {
        $this->correo_proveedor = $correo_proveedor;
    }

    public function getTipoRubro(): string
    {
        return $this->tipo_rubro;
    }

    public function setTipoRubro(string $tipo_rubro): void
    {
        $this->tipo_rubro = $tipo_rubro;
    }

    public function getDireccionProveedor(): string
    {
        return $this->direccion_proveedor;
    }

    public function setDireccionProveedor(string $direccion_proveedor): void
    {
        $this->direccion_proveedor = $direccion_proveedor;
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
