<?php

namespace App\traits;

/**
 * Trait CasheaTrait
 * Validaciones del módulo de créditos Cashea.
 */
trait CasheaTrait
{
    private static $porcentajesInicialValidos = [0, 10, 20, 30, 40, 50, 60];

    /**
     * Valida los datos de un crédito Cashea.
     */
    protected function validarCreditoCashea(array $datos)
    {
        $idPedido = (int) ($datos['id_pedido'] ?? 0);
        if ($idPedido <= 0) {
            return ['status' => 'error', 'message' => 'Debe seleccionar un pedido existente'];
        }

        $pedido = $this->obtenerPedidoActivo($idPedido);
        if (!$pedido) {
            return ['status' => 'error', 'message' => 'El pedido seleccionado no existe o no está activo'];
        }

        $montoTotal = round((float) ($pedido['monto_total'] ?? 0), 2);
        if ($montoTotal <= 0) {
            return ['status' => 'error', 'message' => 'El monto total del pedido debe ser mayor a cero'];
        }

        $nivel = (int) ($datos['nivel_cashea'] ?? 0);
        if ($nivel <= 0) {
            return ['status' => 'error', 'message' => 'Debe indicar una cantidad de cuotas mayor a 0'];
        }

        $porcentajeInicial = $datos['porcentaje_inicial'] ?? null;
        if ($porcentajeInicial === null || !in_array((int) $porcentajeInicial, self::$porcentajesInicialValidos, true)) {
            return ['status' => 'error', 'message' => 'Debe seleccionar un porcentaje inicial válido'];
        }

        $idActual = isset($datos['id_cashea_credito']) ? (int) $datos['id_cashea_credito'] : null;
        if ($this->pedidoYaTieneCredito($idPedido, $idActual)) {
            return ['status' => 'error', 'message' => 'El pedido seleccionado ya tiene un crédito Cashea registrado'];
        }

        $porcentajeInicial = (int) $porcentajeInicial;
        $montoInicial = round($montoTotal * ($porcentajeInicial / 100), 2);
        $montoFinanciado = round($montoTotal - $montoInicial, 2);

        if ($montoFinanciado <= 0) {
            return ['status' => 'error', 'message' => 'El monto financiado debe ser mayor a cero'];
        }

        return true;
    }

    protected function obtenerPedidoActivo(int $idPedido): ?array
    {
        try {
            $stmt = $this->conex->prepare(
                'SELECT codigo_pedido, monto_total, estado
                 FROM pedido
                 WHERE codigo_pedido = ? AND estado = 1'
            );
            $stmt->execute([$idPedido]);
            $pedido = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $pedido ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    protected function pedidoYaTieneCredito(int $idPedido, ?int $idActual = null): bool
    {
        try {
            if ($idActual !== null && $idActual > 0) {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM cashea_credito
                     WHERE id_pedido = ? AND id_cashea_credito <> ? AND estado = 1'
                );
                $stmt->execute([$idPedido, $idActual]);
            } else {
                $stmt = $this->conex->prepare(
                    'SELECT COUNT(*) FROM cashea_credito WHERE id_pedido = ? AND estado = 1'
                );
                $stmt->execute([$idPedido]);
            }
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }

    protected function calcularMontosCashea(float $montoTotal, int $porcentajeInicial): array
    {
        $montoInicial = round($montoTotal * ($porcentajeInicial / 100), 2);
        $montoFinanciado = round($montoTotal - $montoInicial, 2);

        return [
            'monto_inicial_pagado'  => $montoInicial,
            'monto_financiado'      => $montoFinanciado,
            'porcentaje_financiado' => 100 - $porcentajeInicial,
            'monto_pendiente'       => $montoFinanciado,
        ];
    }

    protected function cuotasPorNivel(int $nivel): int
    {
        return $nivel;
    }
}
