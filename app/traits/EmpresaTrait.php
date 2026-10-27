<?php

namespace App\traits;

/**
 * Trait EmpresaTrait
 * Validaciones del módulo de Empresa (registro único de configuración).
 */
trait EmpresaTrait
{
    /**
     * Valida los datos fiscales de la empresa.
     */
    protected function validarEmpresa(array $datos)
    {
        // Campos obligatorios
        foreach (['rif_empresa', 'nombre_empresa', 'telefono', 'correo', 'direccion'] as $campo) {
            if (!isset($datos[$campo]) || trim((string) $datos[$campo]) === '') {
                return ['status' => 'error', 'message' => 'Todos los datos fiscales de la empresa son obligatorios'];
            }
        }

        // RIF válido
        if (!preg_match('/^[JVGPE]-[0-9]{8}-[0-9]{1}$/', trim((string) $datos['rif_empresa']))) {
            return ['status' => 'error', 'message' => 'El RIF de la empresa no tiene un formato válido (ej. J-12345678-9)'];
        }

        // Correo con formato válido
        if (!filter_var(trim((string) $datos['correo']), FILTER_VALIDATE_EMAIL)) {
            return ['status' => 'error', 'message' => 'El correo electrónico de la empresa no tiene un formato válido'];
        }

        // Registro único de configuración
        $idEmpresa = isset($datos['codigo_empresa']) ? (int) $datos['codigo_empresa'] : null;
        if ($this->existeOtraEmpresa($idEmpresa)) {
            return ['status' => 'error', 'message' => 'La empresa ya está registrada (registro único de configuración)'];
        }

        return true;
    }

    /**
     * Verifica si ya existe una empresa registrada (registro único).
     */
    protected function existeOtraEmpresa(?int $idEmpresa = null): bool
    {
        try {
            if ($idEmpresa !== null) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM empresa WHERE codigo_empresa <> ?'
                );
                $stmt->execute([$idEmpresa]);
            } else {
                $stmt = $this->conex->prepare('SELECT COUNT(*) FROM empresa');
                $stmt->execute();
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
