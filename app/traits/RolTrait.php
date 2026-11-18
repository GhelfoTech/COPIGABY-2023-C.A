<?php

namespace App\traits;

/**
 * Trait RolTrait
 * Validaciones del módulo de Roles.
 */
trait RolTrait
{
    use ValidacionesTrait;

    /**
     * Valida los datos de un rol.
     */
    protected function validarRol(array $datos)
    {
        // Campos obligatorios
        if (!$this->campoObligatorioCompleto($datos, 'nombre_rol')) {
            return ['status' => 'error', 'message' => 'El nombre del rol es obligatorio'];
        }
        if (!$this->campoObligatorioCompleto($datos, 'descripcion')) {
            return ['status' => 'error', 'message' => 'La descripción del rol es obligatoria'];
        }

        // Unicidad: nombres de roles únicos (ej. admin, empleado)
        $idRol = isset($datos['codigo_rol']) ? (int) $datos['codigo_rol'] : null;
        if ($this->nombreRolDuplicado(trim((string) $datos['nombre_rol']), $idRol)) {
            return ['status' => 'error', 'message' => 'Ya existe un rol con ese nombre'];
        }

        return true;
    }

    /**
     * Verifica si el nombre del rol ya existe.
     */
    protected function nombreRolDuplicado(string $nombreRol, ?int $idRol = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM rol WHERE nombre_rol = ?';
        $parametros = [$nombreRol];
        if ($idRol !== null) {
            $consulta .= ' AND codigo_rol <> ?';
            $parametros[] = $idRol;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }
}
