<?php

namespace App\models;

use App\config\ConectDB;
use App\traits\CasheaTrait;
use PDO;
use PDOException;

class casheaModel extends ConectDB {
    use CasheaTrait;
    private $conex;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
    }

    /**
     * Lista todos los créditos Cashea con datos del pedido y del cliente.
     */
    public function getAllCreditos() {
        try {
            $query = "SELECT cc.id_cashea_credito, cc.id_pedido, cc.nivel_cashea,
                             cc.monto_inicial_pagado, cc.monto_financiado, cc.porcentaje_financiado,
                             cc.monto_pendiente, cc.fecha, cc.estado,
                             p.monto_total, p.fecha_pedido, p.cedula_cliente,
                             c.nombre AS nombre_cliente
                      FROM cashea_credito cc
                      INNER JOIN pedido p ON p.codigo_pedido = cc.id_pedido
                      LEFT JOIN cliente c ON c.cedula_cliente = p.cedula_cliente
                      ORDER BY cc.id_cashea_credito DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            $creditos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($creditos as &$credito) {
                $nivel = (int) $credito['nivel_cashea'];
                $cuotas = $this->cuotasPorNivel($nivel);
                $financiado = (float) $credito['monto_financiado'];
                $credito['cantidad_cuotas'] = $cuotas;
                $credito['monto_cuota'] = $cuotas > 0 ? round($financiado / $cuotas, 2) : 0;
                $credito['cuotas_pendientes'] = $this->calcularCuotasPendientes(
                    (float) $credito['monto_pendiente'],
                    $financiado,
                    $cuotas
                );
                $credito['porcentaje_inicial'] = 100 - (int) $credito['porcentaje_financiado'];
            }

            return $creditos;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Resumen de deuda pendiente por cliente.
     * Solo considera créditos activos y saldos mayores que cero.
     * El saldo proviene de cashea_credito.monto_pendiente, que se actualiza
     * en cada abono mediante registrarPago().
     */
    public function getResumenDeudaPendiente(): array {
        try {
            $query = "SELECT
                          p.cedula_cliente,
                          COALESCE(c.nombre, 'Cliente no identificado') AS nombre_cliente,
                          SUM(GREATEST(COALESCE(cc.monto_pendiente, 0), 0)) AS deuda_pendiente,
                          COUNT(cc.id_cashea_credito) AS creditos_pendientes
                      FROM cashea_credito cc
                      INNER JOIN pedido p ON p.codigo_pedido = cc.id_pedido
                      LEFT JOIN cliente c ON c.cedula_cliente = p.cedula_cliente
                      WHERE cc.estado = 1
                        AND COALESCE(cc.monto_pendiente, 0) > 0
                      GROUP BY p.cedula_cliente, c.nombre
                      ORDER BY deuda_pendiente DESC, nombre_cliente ASC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = 0.0;
            foreach ($clientes as &$cliente) {
                $cliente['deuda_pendiente'] = round((float) $cliente['deuda_pendiente'], 2);
                $cliente['creditos_pendientes'] = (int) $cliente['creditos_pendientes'];
                $total += $cliente['deuda_pendiente'];
            }
            unset($cliente);

            return [
                'total_deuda' => round($total, 2),
                'cantidad_clientes' => count($clientes),
                'clientes' => $clientes,
            ];
        } catch (PDOException $e) {
            return [
                'total_deuda' => 0.0,
                'cantidad_clientes' => 0,
                'clientes' => [],
            ];
        }
    }

    /**
     * Obtiene un crédito por su identificador.
     */
    public function getCreditoById($id) {
        try {
            $query = "SELECT cc.id_cashea_credito, cc.id_pedido, cc.nivel_cashea,
                             cc.monto_inicial_pagado, cc.monto_financiado, cc.porcentaje_financiado,
                             cc.monto_pendiente, cc.fecha, cc.estado,
                             p.monto_total, p.fecha_pedido, p.cedula_cliente,
                             c.nombre AS nombre_cliente
                      FROM cashea_credito cc
                      INNER JOIN pedido p ON p.codigo_pedido = cc.id_pedido
                      LEFT JOIN cliente c ON c.cedula_cliente = p.cedula_cliente
                      WHERE cc.id_cashea_credito = ?";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([(int) $id]);
            $credito = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$credito) {
                return null;
            }

            $nivel = (int) $credito['nivel_cashea'];
            $cuotas = $this->cuotasPorNivel($nivel);
            $financiado = (float) $credito['monto_financiado'];
            $credito['cantidad_cuotas'] = $cuotas;
            $credito['monto_cuota'] = $cuotas > 0 ? round($financiado / $cuotas, 2) : 0;
            $credito['cuotas_pendientes'] = $this->calcularCuotasPendientes(
                (float) $credito['monto_pendiente'],
                $financiado,
                $cuotas
            );
            $credito['porcentaje_inicial'] = 100 - (int) $credito['porcentaje_financiado'];
            return $credito;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Pedidos activos sin crédito Cashea vigente (lectura simple de pedido).
     */
    public function getPedidosDisponibles($idPedidoActual = null) {
        try {
            $query = "SELECT p.codigo_pedido, p.monto_total, p.fecha_pedido, p.cedula_cliente,
                             c.nombre AS nombre_cliente
                      FROM pedido p
                      LEFT JOIN cliente c ON c.cedula_cliente = p.cedula_cliente
                      WHERE p.estado = 1
                        AND (
                            NOT EXISTS (
                                SELECT 1 FROM cashea_credito cc
                                WHERE cc.id_pedido = p.codigo_pedido AND cc.estado = 1
                            )
                            OR p.codigo_pedido = ?
                        )
                      ORDER BY p.codigo_pedido DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute([(int) $idPedidoActual]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Registra un crédito Cashea sobre un pedido existente.
     */
    public function addCredito($datos) {
        $validacion = $this->validarCreditoCashea($datos);
        if ($validacion !== true) {
            return $validacion;
        }

        $pedido = $this->obtenerPedidoActivo((int) $datos['id_pedido']);
        $porcentajeInicial = (float) $datos['porcentaje_inicial'];
        $montos = $this->calcularMontosCashea(round((float) $pedido['monto_total'], 2), $porcentajeInicial);

        try {
            $query = "INSERT INTO cashea_credito
                        (id_pedido, nivel_cashea, monto_inicial_pagado, monto_financiado,
                         porcentaje_financiado, monto_pendiente, fecha, estado)
                      VALUES (?, ?, ?, ?, ?, ?, NOW(), 1)";
            $stmt = $this->conex->prepare($query);
            $ejecutado = $stmt->execute([
                (int) $datos['id_pedido'],
                (int) $datos['nivel_cashea'],
                number_format($montos['monto_inicial_pagado'], 2, '.', ''),
                number_format($montos['monto_financiado'], 2, '.', ''),
                $montos['porcentaje_financiado'],
                number_format($montos['monto_pendiente'], 2, '.', ''),
            ]);

            return $ejecutado
                ? ['status' => 'success', 'message' => 'Crédito Cashea registrado con éxito.']
                : ['status' => 'error', 'message' => 'No se pudo registrar el crédito Cashea.'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Actualiza un crédito Cashea. No modifica la tabla pedido.
     */
    public function updateCredito($id, $datos) {
        $datos['id_cashea_credito'] = (int) $id;
        $validacion = $this->validarCreditoCashea($datos);
        if ($validacion !== true) {
            return $validacion;
        }

        $pedido = $this->obtenerPedidoActivo((int) $datos['id_pedido']);
        $porcentajeInicial = (float) $datos['porcentaje_inicial'];
        $montos = $this->calcularMontosCashea(round((float) $pedido['monto_total'], 2), $porcentajeInicial);
        $estado = isset($datos['estado']) ? (int) $datos['estado'] : 1;

        try {
            $actual = $this->getCreditoById($id);
            $montoPendiente = $montos['monto_pendiente'];
            if ($actual) {
                $pendienteActual = (float) $actual['monto_pendiente'];
                $montoPendiente = min($pendienteActual, $montos['monto_financiado']);
            }

            $query = "UPDATE cashea_credito
                      SET id_pedido = ?, nivel_cashea = ?, monto_inicial_pagado = ?,
                          monto_financiado = ?, porcentaje_financiado = ?,
                          monto_pendiente = ?, estado = ?
                      WHERE id_cashea_credito = ?";
            $stmt = $this->conex->prepare($query);
            $ejecutado = $stmt->execute([
                (int) $datos['id_pedido'],
                (int) $datos['nivel_cashea'],
                number_format($montos['monto_inicial_pagado'], 2, '.', ''),
                number_format($montos['monto_financiado'], 2, '.', ''),
                $montos['porcentaje_financiado'],
                number_format($montoPendiente, 2, '.', ''),
                $estado,
                (int) $id,
            ]);

            return $ejecutado
                ? ['status' => 'success', 'message' => 'Crédito Cashea actualizado.']
                : ['status' => 'error', 'message' => 'No se pudo actualizar el crédito Cashea.'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Registra un abono/pago de cuota sobre un crédito Cashea.
     */
    public function registrarPago($idCredito, $montoAbonado) {
        try {
            $this->conex->beginTransaction();

            $credito = $this->getCreditoById($idCredito);
            if (!$credito) {
                $this->conex->rollBack();
                return ['status' => 'error', 'message' => 'El crédito Cashea no existe.'];
            }

            $montoAbonado = round((float) $montoAbonado, 2);
            $pendiente = round((float) $credito['monto_pendiente'], 2);

            if ($montoAbonado <= 0) {
                $this->conex->rollBack();
                return ['status' => 'error', 'message' => 'El monto abonado debe ser mayor a cero.'];
            }

            if ($montoAbonado > $pendiente) {
                $this->conex->rollBack();
                return ['status' => 'error', 'message' => 'El monto abonado no puede ser mayor a la deuda pendiente.'];
            }

            $stmtDeposito = $this->conex->prepare(
                'INSERT INTO cashea_deposito (fecha_deposito, monto_recibido) VALUES (NOW(), ?)'
            );
            $stmtDeposito->execute([number_format($montoAbonado, 2, '.', '')]);
            $idDeposito = (int) $this->conex->lastInsertId();

            $stmtDetalle = $this->conex->prepare(
                'INSERT INTO cashea_deposito_detalle (id_cashea_credito, id_cashea_deposito, monto_aplicado)
                 VALUES (?, ?, ?)'
            );
            $stmtDetalle->execute([
                (int) $idCredito,
                $idDeposito,
                number_format($montoAbonado, 2, '.', ''),
            ]);

            $stmtUpdate = $this->conex->prepare(
                'UPDATE cashea_credito SET monto_pendiente = monto_pendiente - ? WHERE id_cashea_credito = ?'
            );
            $stmtUpdate->execute([
                number_format($montoAbonado, 2, '.', ''),
                (int) $idCredito,
            ]);

            $this->conex->commit();
            return ['status' => 'success', 'message' => 'Pago registrado con éxito.'];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Desactivación lógica del crédito.
     */
    public function deleteCredito($id) {
        try {
            $stmt = $this->conex->prepare("UPDATE cashea_credito SET estado = 0 WHERE id_cashea_credito = ?");
            return $stmt->execute([(int) $id])
                ? ['status' => 'success', 'message' => 'Crédito Cashea desactivado.']
                : ['status' => 'error', 'message' => 'No se pudo desactivar el crédito Cashea.'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}