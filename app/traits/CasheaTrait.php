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

        $validacionMonto = $this->validarMontoPedidoCashea($pedido['monto_total'] ?? null);
        if ($validacionMonto !== true) {
            return ['status' => 'error', 'message' => $validacionMonto];
        }
        $montoTotal = round((float) $pedido['monto_total'], 2);

        $nivel = (int) ($datos['nivel_cashea'] ?? 0);
        if ($nivel < 1 || $nivel > 6) {
            return ['status' => 'error', 'message' => 'La cantidad de cuotas debe estar entre 1 y 6'];
        }

        $porcentajeInicial = $datos['porcentaje_inicial'] ?? null;
        if (
            !is_scalar($porcentajeInicial)
            || !ctype_digit((string) $porcentajeInicial)
            || !in_array((int) $porcentajeInicial, self::$porcentajesInicialValidos, true)
        ) {
            return ['status' => 'error', 'message' => 'Debe seleccionar un porcentaje inicial válido'];
        }

        $idActual = isset($datos['id_cashea_credito']) ? (int) $datos['id_cashea_credito'] : null;
        if ($this->pedidoYaTieneCredito($idPedido, $idActual)) {
            return ['status' => 'error', 'message' => 'El pedido seleccionado ya tiene un crédito Cashea registrado'];
        }

        $montos = $this->calcularMontosCashea($montoTotal, (float) $porcentajeInicial);
        if ($montos['monto_financiado'] <= 0) {
            return ['status' => 'error', 'message' => 'El monto financiado debe ser mayor a cero'];
        }

        return true;
    }

    protected function validarMontoPedidoCashea($monto): bool|string
    {
        if (!is_numeric($monto) || !is_finite((float) $monto)) {
            return 'El monto total del pedido no es válido';
        }

        if (round((float) $monto, 2) < 25) {
            return 'Cashea solo está disponible para pedidos de $25 o más';
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
            error_log('[CasheaTrait::obtenerPedidoActivo] ' . $e->getMessage());
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
            error_log('[CasheaTrait::pedidoYaTieneCredito] ' . $e->getMessage());
            return false;
        }
    }

    protected function calcularMontosCashea(float $montoTotal, int|float $porcentajeInicial): array
    {
        $montoTotal = round((float) $montoTotal, 2);
        $porcentajeInicial = (float) $porcentajeInicial;
        $totalCents = (int) round($montoTotal * 100);
        $inicialCents = (int) round($totalCents * ($porcentajeInicial / 100));
        $financiadoCents = $totalCents - $inicialCents;

        $montoInicial = round($inicialCents / 100, 2);
        $montoFinanciado = round($financiadoCents / 100, 2);

        return [
            'monto_inicial_pagado'  => $montoInicial,
            'monto_financiado'      => $montoFinanciado,
            'porcentaje_financiado' => (int) round(100 - $porcentajeInicial),
            'monto_pendiente'       => $montoFinanciado,
        ];
    }

    protected function cuotasPorNivel(int $nivel): int
    {
        return $nivel;
    }

    /**
     * Cuotas pendientes según el saldo, usando centavos y el residuo de la última cuota.
     * Nunca supera la cantidad contratada en nivel_cashea.
     */
    protected function calcularCuotasPendientes(float $montoPendiente, float $montoFinanciado, int $cantidadCuotas): int
    {
        $cantidadCuotas = max(0, $cantidadCuotas);
        if ($cantidadCuotas === 0) {
            return 0;
        }

        $pendienteCents = (int) round($montoPendiente * 100);
        $financiadoCents = (int) round($montoFinanciado * 100);

        if ($pendienteCents <= 0 || $financiadoCents <= 0) {
            return 0;
        }

        if ($pendienteCents >= $financiadoCents) {
            return $cantidadCuotas;
        }

        $cuotaBaseCents = intdiv($financiadoCents, $cantidadCuotas);
        $residuoCents = $financiadoCents % $cantidadCuotas;
        $restantePagado = $financiadoCents - $pendienteCents;
        $cuotasPagadas = 0;

        for ($i = 1; $i <= $cantidadCuotas; $i++) {
            $montoCuotaCents = ($i < $cantidadCuotas)
                ? $cuotaBaseCents
                : ($cuotaBaseCents + $residuoCents);

            if ($montoCuotaCents <= 0) {
                break;
            }

            if ($restantePagado >= $montoCuotaCents) {
                $restantePagado -= $montoCuotaCents;
                $cuotasPagadas++;
            } else {
                break;
            }
        }

        return $cantidadCuotas - $cuotasPagadas;
    }
}
