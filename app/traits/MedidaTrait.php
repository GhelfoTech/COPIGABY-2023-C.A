<?php

namespace App\traits;

/**
 * Trait MedidaTrait
 * Validaciones del módulo de Unidad de Medida.
 */
trait MedidaTrait
{
    use ValidacionesTrait;

    /**
     * Valida los datos de una unidad de medida.
     */
    protected function validarMedida(array $datos)
    {
        // Campos obligatorios
        if (!$this->camposObligatoriosCompletos($datos, ['nombre', 'abreviatura', 'cantidad_unidad'])) {
            return ['status' => 'error', 'message' => 'Nombre, abreviatura y cantidad por unidad son obligatorios'];
        }

        // Cantidad por unidad debe ser mayor que cero
        if (!$this->esNumeroValido($datos['cantidad_unidad']) || (int) $datos['cantidad_unidad'] <= 0) {
            return ['status' => 'error', 'message' => 'La cantidad por unidad debe ser un número mayor que cero'];
        }

        // Unicidad: nombres y abreviaturas únicas (ej. BULTO - BLT, DOCENA - DOC)
        $idMedida = isset($datos['codigo_media']) ? (int) $datos['codigo_media'] : null;
        if ($this->nombreMedidaDuplicado(trim((string) $datos['nombre']), $idMedida)) {
            return ['status' => 'error', 'message' => 'Ya existe una unidad de medida con ese nombre'];
        }
        if ($this->abreviaturaDuplicada(trim((string) $datos['abreviatura']), $idMedida)) {
            return ['status' => 'error', 'message' => 'Ya existe una unidad de medida con esa abreviatura'];
        }

        return true;
    }

    /**
     * Verifica si el nombre de la unidad de medida ya existe.
     */
    protected function nombreMedidaDuplicado(string $nombre, ?int $idMedida = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM unidad_medida WHERE nombre = ?';
        $parametros = [$nombre];
        if ($idMedida !== null) {
            $consulta .= ' AND codigo_media <> ?';
            $parametros[] = $idMedida;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }

    /**
     * Verifica si la abreviatura ya existe.
     */
    protected function abreviaturaDuplicada(string $abreviatura, ?int $idMedida = null): bool
    {
        $consulta = 'SELECT COUNT(*) FROM unidad_medida WHERE abreviatura = ?';
        $parametros = [$abreviatura];
        if ($idMedida !== null) {
            $consulta .= ' AND codigo_media <> ?';
            $parametros[] = $idMedida;
        }

        return $this->registroDuplicadoPorConsulta($consulta, $parametros);
    }
}
