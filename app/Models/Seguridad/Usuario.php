<?php

declare(strict_types=1);

namespace App\Models\Seguridad;

use App\Models\Core\ConexionBD;

abstract class Usuario extends ConexionBD
{
    protected string $codigo_usuario;
    protected string $nombre_usuario;
    protected string $contrasena_usuario;
    protected bool $estado;
    protected int $codigo_rol;

    public function __construct(
        string $codigo_usuario,
        string $nombre_usuario,
        string $contrasena_usuario,
        int $codigo_rol,
        bool $estado = true,
        string $host = 'localhost',
        string $dbname = 'copigaby',
        string $username = 'root',
        string $password = ''
    ) {
        parent::__construct($host, $dbname, $username, $password);
        $this->codigo_usuario = $codigo_usuario;
        $this->nombre_usuario = $nombre_usuario;
        $this->contrasena_usuario = $contrasena_usuario;
        $this->codigo_rol = $codigo_rol;
        $this->estado = $estado;
    }

    abstract public function obtenerTipoUsuario(): string;

    public function validarAcceso(string $password): bool
    {
        if (!$this->estado) {
            return false;
        }

        return password_verify($password, $this->contrasena_usuario)
            || hash_equals($this->contrasena_usuario, $password);
    }

    public function getCodigoUsuario(): string
    {
        return $this->codigo_usuario;
    }

    public function setCodigoUsuario(string $codigo_usuario): void
    {
        $this->codigo_usuario = $codigo_usuario;
    }

    public function getNombreUsuario(): string
    {
        return $this->nombre_usuario;
    }

    public function setNombreUsuario(string $nombre_usuario): void
    {
        $this->nombre_usuario = $nombre_usuario;
    }

    public function getContrasenaUsuario(): string
    {
        return $this->contrasena_usuario;
    }

    public function setContrasenaUsuario(string $contrasena_usuario): void
    {
        $this->contrasena_usuario = $contrasena_usuario;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): void
    {
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
}
