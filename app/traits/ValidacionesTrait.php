<?php

namespace App\traits;

trait ValidacionesTrait
{
    private const PORCENTAJES_INICIALES_CASHEA = [0, 10, 20, 30, 40, 50, 60];

    protected function campoObligatorioCompleto(array $datos, string $campo): bool
    {
        return isset($datos[$campo]) && trim((string) $datos[$campo]) !== '';
    }

    protected function camposObligatoriosCompletos(array $datos, array $campos): bool
    {
        foreach ($campos as $campo) {
            if (!$this->campoObligatorioCompleto($datos, $campo)) {
                return false;
            }
        }

        return true;
    }

    protected function telefonoNacionalValido($telefono): bool
    {
        $telefono = preg_replace('/\s+/', '', (string) $telefono);
        return preg_match('/^[0-9]{11}$/', $telefono) === 1;
    }

    protected function correoComValido(string $correo): bool
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL) !== false
            && preg_match('/\.com$/i', $correo) === 1;
    }

    protected function esNumeroValido($valor): bool
    {
        return is_numeric($valor);
    }

    protected function numeroFinitoValido($valor): bool
    {
        return $this->esNumeroValido($valor) && is_finite((float) $valor);
    }

    protected function soloDigitos($valor): bool
    {
        return preg_match('/^[0-9]+$/', trim((string) $valor)) === 1;
    }

    protected function enteroPositivoFinito($valor): bool
    {
        $valor = (float) $valor;
        return is_finite($valor) && $valor > 0 && floor($valor) === $valor;
    }

    protected function parsearFechaYmd(string $fecha): ?\DateTimeImmutable
    {
        $fecha = trim($fecha);
        $fechaParseada = \DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
        if ($fechaParseada === false || $fechaParseada->format('Y-m-d') !== $fecha) {
            return null;
        }

        return $fechaParseada;
    }

    protected function porcentajeInicialCasheaValido(int $porcentaje): bool
    {
        return in_array($porcentaje, self::PORCENTAJES_INICIALES_CASHEA, true);
    }

    protected function registroDuplicadoPorConsulta(string $consulta, array $parametros): bool
    {
        try {
            $stmt = $this->conex->prepare($consulta);
            $stmt->execute($parametros);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            error_log('[ValidacionesTrait::registroDuplicadoPorConsulta] ' . $e->getMessage());
            return false;
        }
    }
}
