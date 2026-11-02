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
                $credito['porcentaje_inicial'] = 100 - (int) $credito['porcentaje_financiado'];
            }

            return $creditos;
        } catch (PDOException $e) {
            return [];
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
            $credito['cantidad_cuotas'] = $cuotas;
            $credito['monto_cuota'] = $cuotas > 0 ? round(((float) $credito['monto_financiado']) / $cuotas, 2) : 0;
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
        $porcentajeInicial = (int) $datos['porcentaje_inicial'];
        $montos = $this->calcularMontosCashea((float) $pedido['monto_total'], $porcentajeInicial);

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
        $porcentajeInicial = (int) $datos['porcentaje_inicial'];
        $montos = $this->calcularMontosCashea((float) $pedido['monto_total'], $porcentajeInicial);
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
