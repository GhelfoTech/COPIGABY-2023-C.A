<?php

    use App\models\casheaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new casheaModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $resultado = $object->addCredito([
                    'id_pedido'           => (int) ($_POST['id_pedido'] ?? 0),
                    'nivel_cashea'        => (int) ($_POST['nivel_cashea'] ?? 0),
                    'porcentaje_inicial'  => $_POST['porcentaje_inicial'] ?? '',
                ]);
                $_SESSION['cashea_flash'] = is_array($resultado)
                    ? $resultado
                    : ['status' => 'error', 'message' => 'No se pudo registrar el crédito Cashea.'];
                header("Location: ?url=cashea");
                exit();
            }
            header("Location: ?url=cashea");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $resultado = $object->updateCredito((int) ($_POST['id_cashea_credito'] ?? 0), [
                    'id_pedido'           => (int) ($_POST['id_pedido'] ?? 0),
                    'nivel_cashea'        => (int) ($_POST['nivel_cashea'] ?? 0),
                    'porcentaje_inicial'  => $_POST['porcentaje_inicial'] ?? null,
                    'estado'              => isset($_POST['estado']) ? 1 : 0,
                ]);
                $_SESSION['cashea_flash'] = is_array($resultado)
                    ? $resultado
                    : ['status' => 'error', 'message' => 'No se pudo actualizar el crédito Cashea.'];
                header("Location: ?url=cashea");
                exit();
            }
            header("Location: ?url=cashea");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteCashea'])) {
                $result = $object->deleteCredito((int) ($_POST['id_cashea_credito'] ?? 0));
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=cashea");
            exit();
        }

        elseif ($_GET['type'] === 'abonar') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cashea_credito'], $_POST['monto_abonado'])) {
                $resultado = $object->registrarPago(
                    (int) $_POST['id_cashea_credito'],
                    is_numeric($_POST['monto_abonado']) ? (float) $_POST['monto_abonado'] : 0
                );
                $_SESSION['cashea_flash'] = is_array($resultado)
                    ? $resultado
                    : ['status' => 'error', 'message' => 'No se pudo registrar el pago.'];
                header("Location: ?url=cashea");
                exit();
            }
            header("Location: ?url=cashea");
            exit();
        }

        elseif ($_GET['type'] === 'details') {
            if (isset($_GET['id'])) {
                $result = $object->getCreditoById((int) $_GET['id']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=cashea");
            exit();
        }

        else {
            header("Location: ?url=cashea");
            exit();
        }
    }

    $creditos = $object->getAllCreditos();
    $resumenDeuda = $object->getResumenDeudaPendiente();
    $pedidosDisponibles = $object->getPedidosDisponibles();
    include 'app/views/cashea/viewCashea.php';