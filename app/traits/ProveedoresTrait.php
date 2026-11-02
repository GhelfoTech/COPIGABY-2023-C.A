<?php

namespace App\traits;

/**
 * Trait ProveedoresTrait
 * Validaciones del módulo de Proveedores.
 */
trait ProveedoresTrait
{
    /**
     * Valida los datos de un proveedor.
     */
    protected function validarProveedor(array $datos)
    {
        // Campos obligatorios
        foreach (['rif_proveedor', 'razon_social', 'telefono', 'correo', 'estado'] as $campo) {
            if (!isset($datos[$campo]) || trim((string) $datos[$campo]) === '') {
                return ['status' => 'error', 'message' => 'Todos los campos obligatorios del proveedor deben ser completados'];
            }
        }

        // Unicidad estricta del RIF
        $idProveedor = isset($datos['codigo_proveedor_actual']) ? (int) $datos['codigo_proveedor_actual'] : null;
        if ($this->rifProveedorDuplicado(trim((string) $datos['rif_proveedor']), $idProveedor)) {
            return ['status' => 'error', 'message' => 'El RIF ya está registrado en el sistema'];
        }

        // Teléfono: formato numérico
        $telefono = preg_replace('/\s+/', '', (string) $datos['telefono']);
        if (!preg_match('/^[0-9]{11}$/', $telefono)) {
            return ['status' => 'error', 'message' => 'El teléfono debe tener 11 dígitos numéricos'];
        }

        // Correo: formato válido
        if (!filter_var(trim((string) $datos['correo']), FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'El correo electrónico no tiene un formato válido'];
        }

        // Evitar duplicidad de correos corporativos
        if ($this->correoProveedorDuplicado(trim((string) $datos['correo']), $idProveedor)) {
            return ['status' => 'error', 'message' => 'El correo ya está registrado para otro proveedor'];
        }

        return true;
    }

    /**
     * Unicidad estricta del RIF (excluyendo un id opcional en actualización).
     */
    protected function rifProveedorDuplicado(string $rif, ?int $idProveedor = null): bool
    {
        try {
            if ($idProveedor !== null) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM proveedor WHERE rif_proveedor = ? AND codigo_proveedor <> ?'
                );
                $stmt->execute([$rif, $idProveedor]);
            } else {
                $stmt = $this->conex->prepare('SELECT COUNT(*) FROM proveedor WHERE rif_proveedor = ?');
                $stmt->execute([$rif]);
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Evita la duplicidad de correos corporativos.
     */
    protected function correoProveedorDuplicado(string $correo, ?int $idProveedor = null): bool
    {
        try {
            if ($idProveedor !== null) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM proveedor WHERE correo = ? AND codigo_proveedor <> ?'
                );
                $stmt->execute([$correo, $idProveedor]);
            } else {
                $stmt = $this->conex->prepare('SELECT COUNT(*) FROM proveedor WHERE correo = ?');
                $stmt->execute([$correo]);
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
