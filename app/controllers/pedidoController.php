<?php

    use App\models\pedidoModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new pedidoModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_cliente'], $_POST['items'])) {
                $items = json_decode($_POST['items'], true);
                if (!is_array($items)) {
                    $items = [];
                }

                $codigoUsuario = $_SESSION['codigo_usuario'] ?? $_SESSION['user_id'];

                $datos = [
                    'codigo_cliente' => (int) $_POST['codigo_cliente'],
                    'codigo_usuario' => (int) $codigoUsuario,
                    'tasa_aplicada'  => isset($_POST['tasa_aplicada'])
                        ? (float) $_POST['tasa_aplicada']
                        : $object->getTasaActual(),
                ];

                $object->addPedido($datos, $items);
                header("Location: ?url=pedido");
                exit();
            }
            header("Location: ?url=pedido");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deletePedido'])) {
                $result = $object->deletePedido((int) $_POST['idpedido']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=pedido");
            exit();
        }

        else {
            header("Location: ?url=pedido");
            exit();
        }
    }

    $pedidos     = $object->getAllPedidos();
    $clientes    = $object->getClientesActivos();
    $productos   = $object->getProductosActivos();
    $servicios   = $object->getServiciosActivos();
    $tasaActual  = $object->getTasaActual();
    include 'app/views/pedidos/viewPedido.php';
