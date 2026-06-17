<?php

    use App\models\pagosModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new pagosModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_pedido'], $_POST['codigo_metodo'])) {
                $object->addPago([
                    'codigo_pedido' => (int) $_POST['codigo_pedido'],
                    'codigo_metodo' => (int) $_POST['codigo_metodo'],
                    'fecha_pago'    => $_POST['fecha_pago'] ?? date('Y-m-d H:i:s'),
                ]);
                header("Location: ?url=pagos");
                exit();
            }
            header("Location: ?url=pagos");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_pago'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $object->updatePago((int) $_POST['codigo_pago'], [
                    'codigo_pedido' => (int) $_POST['codigo_pedido'],
                    'codigo_metodo' => (int) $_POST['codigo_metodo'],
                    'fecha_pago'    => $_POST['fecha_pago'],
                    'estado'        => $estado,
                ]);
                header("Location: ?url=pagos");
                exit();
            }
            header("Location: ?url=pagos");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deletePago'])) {
                $result = $object->deletePago((int) $_POST['codigo_pago']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=pagos");
            exit();
        }

        elseif ($_GET['type'] === 'details') {
            if (isset($_GET['id'])) {
                $result = $object->getPagoById((int) $_GET['id']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
        }

        else {
            header("Location: ?url=pagos");
            exit();
        }
    }

    $pagos = $object->getAllPagos();
    $pedidos = $object->getPedidosActivos();
    $metodos = $object->getMetodosActivos();
    include 'app/views/pagos/viewPagos.php';
