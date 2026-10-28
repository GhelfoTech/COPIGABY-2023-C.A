<?php

namespace App\Models;

use App\config\ConectDB;
use App\Models\monedaModel;
use PDO;
use PDOException;
use DateTime;

class casheaModel extends ConectDB {

    private $conex;
    private $monedaModel;

    public function __construct() {
        parent::__construct();
        $this->conex = $this->getConnection();
        $this->monedaModel = new monedaModel();
    }

    /**
     * Retorna la tasa de cambio activa en Bolívares por USD.
     */
    public function getTasaActual(): float {
        try {
            return (float)$this->monedaModel->getTasaActual();
        } catch (\Throwable $e) {
            return 1.00;
        }
    }

    /**
     * Devuelve las reglas estándar de Cashea por nivel (Ingeniería Inversa)
     */
    public static function getNivelesReglas(): array {
        return [
            1 => [
                'nivel' => 1,
                'nombre' => 'Nivel 1 (Semilla)',
                'porcentaje_inicial' => 60,
                'porcentaje_financiado' => 40,
                'cuotas_permitidas' => [3],
                'cuotas_default' => 3,
                'requisito' => 'Nivel inicial de registro',
                'descripcion' => 'Inicial 60% y 3 cuotas quincenales.'
            ],
            2 => [
                'nivel' => 2,
                'nombre' => 'Nivel 2 (Raíz)',
                'porcentaje_inicial' => 50,
                'porcentaje_financiado' => 50,
                'cuotas_permitidas' => [3],
                'cuotas_default' => 3,
                'requisito' => '5 cuotas a tiempo o $120 en compras',
                'descripcion' => 'Inicial 50% y 3 cuotas quincenales.'
            ],
            3 => [
                'nivel' => 3,
                'nombre' => 'Nivel 3 (Hoja)',
                'porcentaje_inicial' => 40,
                'porcentaje_financiado' => 60,
                'cuotas_permitidas' => [3, 6],
                'cuotas_default' => 3,
                'requisito' => '10 cuotas a tiempo o $400 en compras',
                'descripcion' => 'Inicial 40% y acceso a Modo Más Cuotas (hasta 6 cuotas).'
            ],
            4 => [
                'nivel' => 4,
                'nombre' => 'Nivel 4 (Tronco)',
                'porcentaje_inicial' => 30,
                'porcentaje_financiado' => 70,
                'cuotas_permitidas' => [3, 6, 9],
                'cuotas_default' => 3,
                'requisito' => '20 cuotas a tiempo o $800 en compras',
                'descripcion' => 'Inicial 30% y financiamiento en 3, 6 o 9 cuotas.'
            ],
            5 => [
                'nivel' => 5,
                'nombre' => 'Nivel 5 (Árbol)',
                'porcentaje_inicial' => 20,
                'porcentaje_financiado' => 80,
                'cuotas_permitidas' => [3, 6, 9, 12],
                'cuotas_default' => 3,
                'requisito' => '40 cuotas a tiempo o $2,000 en compras',
                'descripcion' => 'Inicial 20% y financiamiento hasta 12 cuotas.'
            ],
            6 => [
                'nivel' => 6,
                'nombre' => 'Nivel 6 (Araguaney)',
                'porcentaje_inicial' => 20,
                'porcentaje_financiado' => 80,
                'cuotas_permitidas' => [3, 6, 9, 12, 15],
                'cuotas_default' => 3,
                'requisito' => '80 cuotas a tiempo o $4,000 en compras',
                'descripcion' => 'Rango máximo: Inicial desde 20% (0% en promociones especiales) y hasta 15 cuotas.'
            ]
        ];
    }

    /**
     * Obtiene todos los créditos Cashea con datos del pedido y del cliente
     */
    public function getAllCasheaCreditos(): array {
        try {
            $tasa = $this->getTasaActual();
            $query = "SELECT c.id_cashea_credito, c.id_pedido, c.monto_inicial_pagado, 
                             c.monto_financiado, c.porcentaje_financiado, c.num_cuotas, c.nivel_cliente,
                             c.monto_pendiente, c.fecha, c.estado, 
                             p.cedula_cliente, p.monto_total as total_pedido, p.fecha_pedido,
                             cl.nombre as nombre_cliente, cl.telefono as telefono_cliente, cl.correo as correo_cliente
                      FROM cashea_credito c 
                      LEFT JOIN pedido p ON c.id_pedido = p.codigo_pedido 
                      LEFT JOIN cliente cl ON p.cedula_cliente = cl.cedula_cliente 
                      ORDER BY c.id_cashea_credito DESC";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            $creditos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Enriquecer con cálculo de cuotas y estado de mora estilo Cashea
            $hoy = new DateTime();
            foreach ($creditos as &$c) {
                $c['tasa_cambio'] = $tasa;
                $c['monto_inicial_pagado'] = (float)$c['monto_inicial_pagado'];
                $c['monto_financiado'] = (float)$c['monto_financiado'];
                $c['monto_pendiente'] = (float)$c['monto_pendiente'];
                $c['total_pagado'] = max(0, $c['monto_financiado'] - $c['monto_pendiente']);
                
                $c['porcentaje_progreso'] = $c['monto_financiado'] > 0 
                    ? min(100, round(($c['total_pagado'] / $c['monto_financiado']) * 100))
                    : 100;

                // Montos en Bolívares
                $c['monto_financiado_bs'] = $c['monto_financiado'] * $tasa;
                $c['monto_pendiente_bs'] = $c['monto_pendiente'] * $tasa;
                $c['monto_inicial_bs'] = $c['monto_inicial_pagado'] * $tasa;

                // Generar cuotas
                $cronograma = $this->calcularCronogramaCuotas(
                    $c['fecha'], 
                    $c['monto_financiado'], 
                    (int)$c['num_cuotas'], 
                    $c['total_pagado'], 
                    $tasa
                );
                $c['cronograma'] = $cronograma;

                // Verificar si tiene alguna cuota vencida sin pagar
                $c['tiene_mora'] = false;
                $c['dias_mora_max'] = 0;
                if ((int)$c['estado'] === 1 && $c['monto_pendiente'] > 0) {
                    foreach ($cronograma as $cuota) {
                        if ($cuota['estado'] === 'Vencida') {
                            $c['tiene_mora'] = true;
                            if ($cuota['dias_mora'] > $c['dias_mora_max']) {
                                $c['dias_mora_max'] = $cuota['dias_mora'];
                            }
                        }
                    }
                }
            }
            unset($c);

            return $creditos;
        } catch (PDOException $e) {
            error_log("Error in getAllCasheaCreditos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calcula el cronograma exacto de cuotas estilo Cashea (cada 14 días)
     */
    public function calcularCronogramaCuotas(string $fechaInicio, float $montoFinanciado, int $numCuotas, float $totalPagado, float $tasa): array {
        if ($numCuotas <= 0) $numCuotas = 3;
        $cuotas = [];
        $montoBaseCuota = round($montoFinanciado / $numCuotas, 2);
        
        $saldoAcumuladoPagado = $totalPagado;
        $hoy = new DateTime(date('Y-m-d'));

        for ($i = 1; $i <= $numCuotas; $i++) {
            // Última cuota absorbe posibles diferencias de centavos
            $montoEstaCuota = ($i === $numCuotas) 
                ? round($montoFinanciado - ($montoBaseCuota * ($numCuotas - 1)), 2) 
                : $montoBaseCuota;

            // Fecha de vencimiento: 14 días por cuota
            $fechaObj = new DateTime($fechaInicio);
            $diasOffset = $i * 14;
            $fechaObj->modify("+{$diasOffset} days");
            $fechaVencimientoStr = $fechaObj->format('Y-m-d');
            $fechaVencimientoObj = new DateTime($fechaVencimientoStr);

            // Estado de la cuota según lo que se ha ido pagando acumulado
            $estadoCuota = 'Pendiente';
            $diasMora = 0;
            $diasRestantes = 0;

            if ($saldoAcumuladoPagado >= $montoEstaCuota) {
                $estadoCuota = 'Pagada';
                $saldoAcumuladoPagado -= $montoEstaCuota;
            } elseif ($saldoAcumuladoPagado > 0) {
                // Parcialmente pagada
                $estadoCuota = 'Parcial';
                $saldoAcumuladoPagado = 0;
            } else {
                if ($hoy > $fechaVencimientoObj) {
                    $estadoCuota = 'Vencida';
                    $interval = $hoy->diff($fechaVencimientoObj);
                    $diasMora = (int)$interval->days;
                } else {
                    $estadoCuota = 'Pendiente';
                    $interval = $fechaVencimientoObj->diff($hoy);
                    $diasRestantes = (int)$interval->days;
                }
            }

            $cuotas[] = [
                'numero_cuota' => $i,
                'fecha_vencimiento' => $fechaVencimientoStr,
                'monto_usd' => $montoEstaCuota,
                'monto_bs' => round($montoEstaCuota * $tasa, 2),
                'estado' => $estadoCuota,
                'dias_mora' => $diasMora,
                'dias_restantes' => $diasRestantes
            ];
        }

        return $cuotas;
    }

    /**
     * Obtiene un crédito por su ID con todo su expediente y abonos
     */
    public function getCasheaCreditoById(int $id): ?array {
        try {
            $tasa = $this->getTasaActual();
            $query = "SELECT c.id_cashea_credito, c.id_pedido, c.monto_inicial_pagado, 
                             c.monto_financiado, c.porcentaje_financiado, c.num_cuotas, c.nivel_cliente,
                             c.monto_pendiente, c.fecha, c.estado, 
                             p.cedula_cliente, p.monto_total as total_pedido, p.fecha_pedido, p.subtotal, p.monto_iva,
                             cl.nombre as nombre_cliente, cl.telefono as telefono_cliente, cl.correo as correo_cliente, cl.direccion as direccion_cliente
                      FROM cashea_credito c 
                      LEFT JOIN pedido p ON c.id_pedido = p.codigo_pedido 
                      LEFT JOIN cliente cl ON p.cedula_cliente = cl.cedula_cliente 
                      WHERE c.id_cashea_credito = :id 
                      LIMIT 1";
            $stmt = $this->conex->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $credito = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$credito) {
                return null;
            }

            $credito['monto_inicial_pagado'] = (float)$credito['monto_inicial_pagado'];
            $credito['monto_financiado'] = (float)$credito['monto_financiado'];
            $credito['monto_pendiente'] = (float)$credito['monto_pendiente'];
            $credito['total_pagado'] = max(0, $credito['monto_financiado'] - $credito['monto_pendiente']);
            $credito['tasa_cambio'] = $tasa;
            $credito['porcentaje_progreso'] = $credito['monto_financiado'] > 0 
                ? min(100, round(($credito['total_pagado'] / $credito['monto_financiado']) * 100))
                : 100;

            // Obtener historial de abonos / depósitos aplicados a este crédito
            $queryDepositos = "SELECT dd.id_cashea_deposito_detalle, dd.monto_aplicado, 
                                      d.id_cashea_deposito, d.fecha_deposito, d.referencia, d.nota
                               FROM cashea_deposito_detalle dd 
                               INNER JOIN cashea_deposito d ON dd.id_cashea_deposito = d.id_cashea_deposito 
                               WHERE dd.id_cashea_credito = :id 
                               ORDER BY d.fecha_deposito DESC, d.id_cashea_deposito DESC";
            $stmtDep = $this->conex->prepare($queryDepositos);
            $stmtDep->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDep->execute();
            $credito['depositos'] = $stmtDep->fetchAll(PDO::FETCH_ASSOC);

            // Cronograma
            $credito['cronograma'] = $this->calcularCronogramaCuotas(
                $credito['fecha'], 
                $credito['monto_financiado'], 
                (int)$credito['num_cuotas'], 
                $credito['total_pagado'], 
                $tasa
            );

            return $credito;
        } catch (PDOException $e) {
            error_log("Error in getCasheaCreditoById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene los pedidos elegibles para registrar un nuevo crédito Cashea
     */
    public function getPedidosDisponibles(): array {
        try {
            // Selecciona pedidos activos que NO tengan ya un crédito Cashea registrado
            $query = "SELECT p.codigo_pedido, p.cedula_cliente, p.fecha_pedido, p.monto_total, 
                             c.nombre as nombre_cliente, c.telefono as telefono_cliente
                      FROM pedido p 
                      LEFT JOIN cliente c ON p.cedula_cliente = c.cedula_cliente 
                      WHERE p.estado = 1 
                        AND p.codigo_pedido NOT IN (SELECT id_pedido FROM cashea_credito WHERE estado = 1)
                      ORDER BY p.codigo_pedido DESC 
                      LIMIT 50";
            $stmt = $this->conex->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getPedidosDisponibles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Registra un nuevo crédito Cashea asociado a un pedido
     */
    public function addCasheaCredito(int $id_pedido, float $monto_inicial, float $monto_financiado, int $porcentaje, int $num_cuotas = 3, int $nivel_cliente = 2): array {
        try {
            // 1. Validar que el pedido exista
            $stmtCheck = $this->conex->prepare("SELECT codigo_pedido, monto_total, cedula_cliente FROM pedido WHERE codigo_pedido = :id LIMIT 1");
            $stmtCheck->bindParam(':id', $id_pedido, PDO::PARAM_INT);
            $stmtCheck->execute();
            $pedido = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$pedido) {
                return ["status" => "error", "message" => "El ID de pedido #{$id_pedido} no existe en el sistema."];
            }

            // 2. Verificar que no exista un crédito activo para este pedido
            $stmtExiste = $this->conex->prepare("SELECT id_cashea_credito FROM cashea_credito WHERE id_pedido = :id AND estado = 1 LIMIT 1");
            $stmtExiste->bindParam(':id', $id_pedido, PDO::PARAM_INT);
            $stmtExiste->execute();
            if ($stmtExiste->fetch()) {
                return ["status" => "error", "message" => "El pedido #{$id_pedido} ya cuenta con un crédito Cashea activo."];
            }

            // 3. Validar montos coherentes
            $totalPedido = (float)$pedido['monto_total'];
            if ($monto_inicial < 0 || $monto_financiado <= 0) {
                return ["status" => "error", "message" => "Los montos de inicial y financiamiento deben ser positivos."];
            }

            if ($num_cuotas <= 0) $num_cuotas = 3;
            if ($nivel_cliente <= 0 || $nivel_cliente > 6) $nivel_cliente = 2;

            $monto_pendiente = $monto_financiado;
            $fecha = date("Y-m-d H:i:s");
            $estado = 1; // Activo

            $query = "INSERT INTO cashea_credito (id_pedido, monto_inicial_pagado, monto_financiado, porcentaje_financiado, num_cuotas, nivel_cliente, monto_pendiente, fecha, estado)
                      VALUES (:id_pedido, :monto_inicial, :monto_financiado, :porcentaje, :num_cuotas, :nivel_cliente, :monto_pendiente, :fecha, :estado)";
            
            $stmt = $this->conex->prepare($query);
            $stmt->bindParam(':id_pedido', $id_pedido, PDO::PARAM_INT);
            $stmt->bindParam(':monto_inicial', $monto_inicial);
            $stmt->bindParam(':monto_financiado', $monto_financiado);
            $stmt->bindParam(':porcentaje', $porcentaje, PDO::PARAM_INT);
            $stmt->bindParam(':num_cuotas', $num_cuotas, PDO::PARAM_INT);
            $stmt->bindParam(':nivel_cliente', $nivel_cliente, PDO::PARAM_INT);
            $stmt->bindParam(':monto_pendiente', $monto_pendiente);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $nuevoId = (int)$this->conex->lastInsertId();
                return [
                    "status" => "success", 
                    "message" => "Crédito Cashea #{$nuevoId} registrado con éxito para el pedido #{$id_pedido}.",
                    "id_cashea_credito" => $nuevoId
                ];
            }
            return ["status" => "error", "message" => "No se pudo registrar el crédito en la base de datos."];
        } catch (PDOException $e) {
            error_log("Error in addCasheaCredito: " . $e->getMessage());
            return ["status" => "error", "message" => "Error de base de datos: " . $e->getMessage()];
        }
    }

    /**
     * Registra un Abono o Liquidación de depósito de Cashea contra un crédito
     */
    public function registrarAbono(int $id_cashea_credito, float $monto, string $referencia = '', string $nota = '', ?string $fecha_deposito = null): array {
        try {
            if ($monto <= 0) {
                return ["status" => "error", "message" => "El monto del abono debe ser mayor a 0."];
            }

            // Obtener el crédito actual
            $stmt = $this->conex->prepare("SELECT id_cashea_credito, id_pedido, monto_financiado, monto_pendiente, estado FROM cashea_credito WHERE id_cashea_credito = :id LIMIT 1");
            $stmt->bindParam(':id', $id_cashea_credito, PDO::PARAM_INT);
            $stmt->execute();
            $credito = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$credito) {
                return ["status" => "error", "message" => "El crédito Cashea #{$id_cashea_credito} no existe."];
            }

            $pendienteActual = (float)$credito['monto_pendiente'];
            if ($pendienteActual <= 0) {
                return ["status" => "error", "message" => "Este crédito ya se encuentra totalmente saldado."];
            }

            $montoAplicar = min($monto, $pendienteActual);
            $nuevoPendiente = max(0, $pendienteActual - $montoAplicar);
            $nuevoEstado = ($nuevoPendiente <= 0.005) ? 0 : 1; // 0 = Cerrado/Completado si pendiente es 0

            $fechaDep = $fecha_deposito ?: date("Y-m-d H:i:s");

            // Iniciar transacción
            $this->conex->beginTransaction();

            // 1. Insertar en cashea_deposito
            $stmtDep = $this->conex->prepare("INSERT INTO cashea_deposito (fecha_deposito, monto_recibido, referencia, nota) VALUES (:fecha, :monto, :ref, :nota)");
            $stmtDep->bindParam(':fecha', $fechaDep);
            $stmtDep->bindParam(':monto', $montoAplicar);
            $stmtDep->bindParam(':ref', $referencia);
            $stmtDep->bindParam(':nota', $nota);
            $stmtDep->execute();
            $idDeposito = (int)$this->conex->lastInsertId();

            // 2. Insertar en cashea_deposito_detalle
            $stmtDet = $this->conex->prepare("INSERT INTO cashea_deposito_detalle (id_cashea_credito, id_cashea_deposito, monto_aplicado) VALUES (:id_credito, :id_deposito, :monto)");
            $stmtDet->bindParam(':id_credito', $id_cashea_credito, PDO::PARAM_INT);
            $stmtDet->bindParam(':id_deposito', $idDeposito, PDO::PARAM_INT);
            $stmtDet->bindParam(':monto', $montoAplicar);
            $stmtDet->execute();

            // 3. Actualizar cashea_credito
            $stmtUp = $this->conex->prepare("UPDATE cashea_credito SET monto_pendiente = :nuevo_pendiente, estado = :estado WHERE id_cashea_credito = :id");
            $stmtUp->bindParam(':nuevo_pendiente', $nuevoPendiente);
            $stmtUp->bindParam(':estado', $nuevoEstado, PDO::PARAM_INT);
            $stmtUp->bindParam(':id', $id_cashea_credito, PDO::PARAM_INT);
            $stmtUp->execute();

            $this->conex->commit();

            return [
                "status" => "success", 
                "message" => "Abono de $" . number_format($montoAplicar, 2) . " procesado correctamente. Saldo restante: $" . number_format($nuevoPendiente, 2),
                "nuevo_pendiente" => $nuevoPendiente,
                "credito_cerrado" => ($nuevoEstado === 0)
            ];
        } catch (PDOException $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            error_log("Error in registrarAbono: " . $e->getMessage());
            return ["status" => "error", "message" => "Error al registrar el abono: " . $e->getMessage()];
        }
    }

    /**
     * Actualiza un crédito Cashea
     */
    public function updateCasheaCredito(int $id, float $monto_inicial, float $monto_financiado, int $porcentaje, int $num_cuotas, int $nivel_cliente, float $monto_pendiente, int $estado): array {
        try {
            $query = "UPDATE cashea_credito 
                      SET monto_inicial_pagado = :monto_inicial, 
                          monto_financiado = :monto_financiado, 
                          porcentaje_financiado = :porcentaje, 
                          num_cuotas = :num_cuotas,
                          nivel_cliente = :nivel_cliente,
                          monto_pendiente = :monto_pendiente, 
                          estado = :estado 
                      WHERE id_cashea_credito = :id";
            
            $stmt = $this->conex->prepare($query);
            $stmt->bindParam(':monto_inicial', $monto_inicial);
            $stmt->bindParam(':monto_financiado', $monto_financiado);
            $stmt->bindParam(':porcentaje', $porcentaje, PDO::PARAM_INT);
            $stmt->bindParam(':num_cuotas', $num_cuotas, PDO::PARAM_INT);
            $stmt->bindParam(':nivel_cliente', $nivel_cliente, PDO::PARAM_INT);
            $stmt->bindParam(':monto_pendiente', $monto_pendiente);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Crédito Cashea #{$id} actualizado correctamente."];
            }
            return ["status" => "error", "message" => "No se pudo actualizar el crédito."];
        } catch (PDOException $e) {
            error_log("Error in updateCasheaCredito: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Cambia el estado de un crédito Cashea (activar / desactivar)
     */
    public function toggleEstadoCasheaCredito(int $id): array {
        try {
            $stmt = $this->conex->prepare("SELECT estado FROM cashea_credito WHERE id_cashea_credito = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $curr = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$curr) {
                return ["status" => "error", "message" => "El crédito no existe."];
            }

            $nuevoEstado = ((int)$curr['estado'] === 1) ? 0 : 1;
            $stmtUp = $this->conex->prepare("UPDATE cashea_credito SET estado = :estado WHERE id_cashea_credito = :id");
            $stmtUp->bindParam(':estado', $nuevoEstado, PDO::PARAM_INT);
            $stmtUp->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtUp->execute();

            $texto = ($nuevoEstado === 1) ? 'reactivado' : 'cerrado/desactivado';
            return ["status" => "success", "message" => "Crédito Cashea {$texto} con éxito."];
        } catch (PDOException $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Elimina lógicamente o físicamente un crédito Cashea
     */
    public function deleteCasheaCredito(int $id): array {
        try {
            // Elimina el crédito y por CASCADE sus detalles asociados
            $query = "DELETE FROM cashea_credito WHERE id_cashea_credito = :id";
            $stmt = $this->conex->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ["status" => "success", "message" => "Crédito Cashea eliminado con éxito."];
            }
            return ["status" => "error", "message" => "No se pudo procesar la eliminación."];
        } catch (PDOException $e) {
            error_log("Error in deleteCasheaCredito: " . $e->getMessage());
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    /**
     * Resumen de estadísticas para las tarjetas de KPIs
     */
    public function getEstadisticas(): array {
        try {
            $creditos = $this->getAllCasheaCreditos();
            $totalCreditos = count($creditos);
            $totalFinanciado = 0.0;
            $totalPendiente = 0.0;
            $totalRecuperado = 0.0;
            $enMora = 0;
            $activos = 0;
            $cerrados = 0;

            foreach ($creditos as $c) {
                $totalFinanciado += $c['monto_financiado'];
                $totalPendiente += $c['monto_pendiente'];
                $totalRecuperado += $c['total_pagado'];
                if ((int)$c['estado'] === 1) {
                    $activos++;
                    if (!empty($c['tiene_mora'])) {
                        $enMora++;
                    }
                } else {
                    $cerrados++;
                }
            }

            $tasa = $this->getTasaActual();

            return [
                'total_creditos' => $totalCreditos,
                'total_financiado' => $totalFinanciado,
                'total_financiado_bs' => $totalFinanciado * $tasa,
                'total_pendiente' => $totalPendiente,
                'total_pendiente_bs' => $totalPendiente * $tasa,
                'total_recuperado' => $totalRecuperado,
                'total_recuperado_bs' => $totalRecuperado * $tasa,
                'en_mora' => $enMora,
                'activos' => $activos,
                'cerrados' => $cerrados,
                'tasa_cambio' => $tasa
            ];
        } catch (\Throwable $e) {
            return [
                'total_creditos' => 0,
                'total_financiado' => 0.0,
                'total_financiado_bs' => 0.0,
                'total_pendiente' => 0.0,
                'total_pendiente_bs' => 0.0,
                'total_recuperado' => 0.0,
                'total_recuperado_bs' => 0.0,
                'en_mora' => 0,
                'activos' => 0,
                'cerrados' => 0,
                'tasa_cambio' => 1.0
            ];
        }
    }
}
