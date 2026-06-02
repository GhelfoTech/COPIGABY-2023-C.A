<?php

declare(strict_types=1);

namespace App\Models\Configuracion;

class Empresa
{
    private string $rif;
    private string $nombre_empresa;
    private string $direccion;
    private string $telefono;

    public function __construct(
        string $rif,
        string $nombre_empresa,
        string $direccion,
        string $telefono
    ) {
        $this->rif = $rif;
        $this->nombre_empresa = $nombre_empresa;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
    }

    public function getRif(): string
    {
        return $this->rif;
    }

    public function setRif(string $rif): void
    {
        $this->rif = $rif;
    }

    public function getNombreEmpresa(): string
    {
        return $this->nombre_empresa;
    }

    public function setNombreEmpresa(string $nombre_empresa): void
    {
        $this->nombre_empresa = $nombre_empresa;
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): void
    {
        $this->direccion = $direccion;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): void
    {
        $this->telefono = $telefono;
    }
}
