<?php

declare(strict_types=1);

namespace App\Models\Seguridad;

class Propietario extends Usuario
{
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
        parent::__construct(
            $codigo_usuario,
            $nombre_usuario,
            $contrasena_usuario,
            $codigo_rol,
            $estado,
            $host,
            $dbname,
            $username,
            $password
        );
    }

    public function obtenerTipoUsuario(): string
    {
        return 'propietario';
    }
}
