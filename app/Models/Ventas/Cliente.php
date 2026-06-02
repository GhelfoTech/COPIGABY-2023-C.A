<?php

declare(strict_types=1);

namespace App\Models\Ventas;

class Cliente
{
    private int $codigo_cliente;
    private string $cedula;
    private string $nombre;
    private string $direccion;
    private string $telefono;

    public function __construct(
        int $codigo_cliente,
        string $cedula,
        string $nombre,
        string $direccion,
        string $telefono
    ) {
        $this->codigo_cliente = $codigo_cliente;
        $this->cedula = $cedula;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
    }

    public function getCodigoCliente(): int
    {
        return $this->codigo_cliente;
    }

    public function setCodigoCliente(int $codigo_cliente): void
    {
        $this->codigo_cliente = $codigo_cliente;
    }

    public function getCedula(): string
    {
        return $this->cedula;
    }

    public function setCedula(string $cedula): void
    {
        $this->cedula = $cedula;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
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
