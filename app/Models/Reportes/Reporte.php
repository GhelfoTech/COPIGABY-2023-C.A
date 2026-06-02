<?php

declare(strict_types=1);

namespace App\Models\Reportes;

class Reporte
{
    private int $id_reporte;
    private string $tipo_reporte;
    private string $fecha_generado;
    private string $codigo_usuario;

    public function __construct(
        int $id_reporte,
        string $tipo_reporte,
        string $fecha_generado,
        string $codigo_usuario
    ) {
        $this->id_reporte = $id_reporte;
        $this->tipo_reporte = $tipo_reporte;
        $this->fecha_generado = $fecha_generado;
        $this->codigo_usuario = $codigo_usuario;
    }

    public function getIdReporte(): int
    {
        return $this->id_reporte;
    }

    public function setIdReporte(int $id_reporte): void
    {
        $this->id_reporte = $id_reporte;
    }

    public function getTipoReporte(): string
    {
        return $this->tipo_reporte;
    }

    public function setTipoReporte(string $tipo_reporte): void
    {
        $this->tipo_reporte = $tipo_reporte;
    }

    public function getFechaGenerado(): string
    {
        return $this->fecha_generado;
    }

    public function setFechaGenerado(string $fecha_generado): void
    {
        $this->fecha_generado = $fecha_generado;
    }

    public function getCodigoUsuario(): string
    {
        return $this->codigo_usuario;
    }

    public function setCodigoUsuario(string $codigo_usuario): void
    {
        $this->codigo_usuario = $codigo_usuario;
    }
}
