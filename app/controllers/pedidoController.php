<?php

    use App\models\pedidoModel;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ?url=login');
        exit();
    }

    $object = new pedidoModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Location: ?url=pedido');
                exit();
            }

            $isAjax = (
                !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) || (isset($_POST['ajax']) && $_POST['ajax'] === '1');

            if (!isset($_POST['codigo_cliente'], $_POST['items'])) {
                $payload = [
                    'status'  => 'error',
                    'message' => 'Datos incompletos: cliente e ítems son obligatorios.',
                ];
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($payload);
                    exit();
                }
                $_SESSION['pedido_flash'] = $payload;
                header('Location: ?url=pedido');
                exit();
            }

            $items = json_decode((string) $_POST['items'], true);
            if (!is_array($items) || $items === []) {
                $payload = [
                    'status'  => 'error',
                    'message' => 'El pedido debe incluir al menos un ítem válido.',
                ];
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($payload);
                    exit();
                }
                $_SESSION['pedido_flash'] = $payload;
                header('Location: ?url=pedido');
                exit();
            }

            $codigoCliente = (int) $_POST['codigo_cliente'];
            if ($codigoCliente <= 0) {
                $payload = [
                    'status'  => 'error',
                    'message' => 'Debe seleccionar un cliente válido.',
                ];
                if ($isAjax) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($payload);
                    exit();
                }
                $_SESSION['pedido_flash'] = $payload;
                header('Location: ?url=pedido');
                exit();
            }

            $datos = [
                'codigo_cliente' => $codigoCliente,
                'codigo_usuario' => (int) $_SESSION['user_id'],
                'tasa_aplicada'  => isset($_POST['tasa_aplicada']) && $_POST['tasa_aplicada'] !== ''
                    ? (float) $_POST['tasa_aplicada']
                    : $object->getTasaActual(),
            ];

            $result = $object->addPedido($datos, $items);

            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($result);
                exit();
            }

            $_SESSION['pedido_flash'] = $result;
            header('Location: ?url=pedido');
            exit();
        }

        if ($_GET['type'] === 'main') {
            if (isset($_POST['deletePedido'])) {
                $result = $object->deletePedido((int) $_POST['idpedido']);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($result);
                exit();
            }
            header('Location: ?url=pedido');
            exit();
        }

        header('Location: ?url=pedido');
        exit();
    }

    $pedidos    = $object->getAllPedidos();
    $clientes   = $object->getClientesActivos();
    $productos  = $object->getProductosActivos();
    $servicios  = $object->getServiciosActivos();
    $tasaActual = $object->getTasaActual();
    $pedidoFlash = $_SESSION['pedido_flash'] ?? null;
    unset($_SESSION['pedido_flash']);

    include 'app/views/pedidos/viewPedido.php';
