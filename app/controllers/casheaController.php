<?php

use App\Models\casheaModel;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ?url=login");
    exit();
}

$object = new casheaModel();
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
          || (isset($_POST['ajax']) && $_POST['ajax'] === '1')
          || (isset($_GET['ajax']) && $_GET['ajax'] === '1');

if (isset($_GET['type'])) {
    $type = $_GET['type'];

    // 1. REGISTRAR NUEVO CRÉDITO CASHEA
    if ($type === 'register') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?url=cashea");
            exit();
        }

        if (empty($_POST['id_pedido'])) {
            $response = ["status" => "error", "message" => "Debe seleccionar un pedido válido."];
        } else {
            $id_pedido             = (int)$_POST['id_pedido'];
            $monto_inicial_pagado  = (float)($_POST['monto_inicial_pagado'] ?? 0);
            $monto_financiado      = (float)($_POST['monto_financiado'] ?? 0);
            $porcentaje_financiado = (int)($_POST['porcentaje_financiado'] ?? 60);
            $num_cuotas            = (int)($_POST['num_cuotas'] ?? 3);
            $nivel_cliente         = (int)($_POST['nivel_cliente'] ?? 2);

            $response = $object->addCasheaCredito(
                $id_pedido, 
                $monto_inicial_pagado, 
                $monto_financiado, 
                $porcentaje_financiado,
                $num_cuotas,
                $nivel_cliente
            );
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        }

        $_SESSION['cashea_flash'] = $response;
        header("Location: ?url=cashea");
        exit();
    }

    // 2. REGISTRAR ABONO / LIQUIDACIÓN DE CASHEA
    elseif ($type === 'deposit' || $type === 'abono') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?url=cashea");
            exit();
        }

        $id_cashea_credito = (int)($_POST['id_cashea_credito'] ?? 0);
        $monto_abono       = (float)($_POST['monto_abono'] ?? 0);
        $referencia        = trim((string)($_POST['referencia'] ?? ''));
        $nota              = trim((string)($_POST['nota'] ?? ''));
        $fecha_deposito    = !empty($_POST['fecha_deposito']) ? $_POST['fecha_deposito'] : date('Y-m-d H:i:s');

        $response = $object->registrarAbono($id_cashea_credito, $monto_abono, $referencia, $nota, $fecha_deposito);

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        }

        $_SESSION['cashea_flash'] = $response;
        header("Location: ?url=cashea");
        exit();
    }

    // 3. ACTUALIZAR CRÉDITO CASHEA
    elseif ($type === 'update') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?url=cashea");
            exit();
        }

        $id_cashea_credito     = (int)($_POST['id_cashea_credito'] ?? 0);
        $monto_inicial_pagado  = (float)($_POST['monto_inicial_pagado'] ?? 0);
        $monto_financiado      = (float)($_POST['monto_financiado'] ?? 0);
        $porcentaje_financiado = (int)($_POST['porcentaje_financiado'] ?? 60);
        $num_cuotas            = (int)($_POST['num_cuotas'] ?? 3);
        $nivel_cliente         = (int)($_POST['nivel_cliente'] ?? 2);
        $monto_pendiente       = (float)($_POST['monto_pendiente'] ?? $monto_financiado);
        $estado                = isset($_POST['estado']) && ($_POST['estado'] === '1' || $_POST['estado'] === 'on') ? 1 : 0;

        $response = $object->updateCasheaCredito(
            $id_cashea_credito,
            $monto_inicial_pagado,
            $monto_financiado,
            $porcentaje_financiado,
            $num_cuotas,
            $nivel_cliente,
            $monto_pendiente,
            $estado
        );

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        }

        $_SESSION['cashea_flash'] = $response;
        header("Location: ?url=cashea");
        exit();
    }

    // 4. ELIMINAR / ANULAR CRÉDITO
    elseif ($type === 'delete' || $type === 'main') {
        if (isset($_POST['deleteCashea']) || isset($_POST['id_cashea_credito'])) {
            $id = (int)($_POST['id_cashea_credito'] ?? 0);
            $action = $_POST['action'] ?? 'delete';

            if ($action === 'toggle') {
                $response = $object->toggleEstadoCasheaCredito($id);
            } else {
                $response = $object->deleteCasheaCredito($id);
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response);
            exit();
        }
    }

    // 5. CONSULTAR DETALLES POR ID (AJAX)
    elseif ($type === 'getDetail') {
        $id = (int)($_REQUEST['id_cashea_credito'] ?? 0);
        $credito = $object->getCasheaCreditoById($id);

        header('Content-Type: application/json; charset=utf-8');
        if ($credito) {
            echo json_encode(['status' => 'success', 'data' => $credito]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Crédito no encontrado.']);
        }
        exit();
    }
}

// Carga de datos para la vista principal
$cashea_creditos     = $object->getAllCasheaCreditos();
$pedidos_disponibles = $object->getPedidosDisponibles();
$estadisticas        = $object->getEstadisticas();
$niveles_cashea      = casheaModel::getNivelesReglas();
$tasaActual          = $object->getTasaActual();

$casheaFlash = $_SESSION['cashea_flash'] ?? null;
unset($_SESSION['cashea_flash']);

include 'app/views/cashea/viewCashea.php';
