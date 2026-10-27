<?php

namespace App\traits;

/**
 * Trait RolTrait
 * Validaciones del módulo de Roles.
 */
trait RolTrait
{
    /**
     * Valida los datos de un rol.
     */
    protected function validarRol(array $datos)
    {
        // Campos obligatorios
        if (!isset($datos['nombre_rol']) || trim((string) $datos['nombre_rol']) === '') {
            return ['status' => 'error', 'message' => 'El nombre del rol es obligatorio'];
        }
        if (!isset($datos['descripcion']) || trim((string) $datos['descripcion']) === '') {
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
        try {
            if ($idRol !== null) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM rol WHERE nombre_rol = ? AND codigo_rol <> ?'
                );
                $stmt->execute([$nombreRol, $idRol]);
            } else {
                $stmt = $this->conex->prepare('SELECT COUNT(*) FROM rol WHERE nombre_rol = ?');
                $stmt->execute([$nombreRol]);
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
