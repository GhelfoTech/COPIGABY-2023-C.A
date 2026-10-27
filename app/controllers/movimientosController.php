<?php

    use App\models\movimientosModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new movimientosModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo'])) {
                $object->addMovimiento([
                    'fecha'          => $_POST['fecha'] ?? date('Y-m-d H:i:s'),
                    'cedula_usuario' => $_POST['cedula_usuario'] ?? $_SESSION['user_id'],
                    'tipo'           => (int) $_POST['tipo'],
                ]);
                header("Location: ?url=movimientos");
                exit();
            }
            header("Location: ?url=movimientos");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_movimiento'])) {
                $object->updateMovimiento((int) $_POST['codigo_movimiento'], [
                    'fecha'          => $_POST['fecha'],
                    'cedula_usuario' => (int) $_POST['cedula_usuario'],
                    'tipo'           => (int) $_POST['tipo'],
                ]);
                header("Location: ?url=movimientos");
                exit();
            }
            header("Location: ?url=movimientos");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteMovimiento'])) {
                $result = $object->deleteMovimiento((int) $_POST['codigo_movimiento']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=movimientos");
            exit();
        }

        elseif ($_GET['type'] === 'details') {
            if (isset($_GET['id'])) {
                $result = $object->getMovimientoById((int) $_GET['id']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
        }

        else {
            header("Location: ?url=movimientos");
            exit();
        }
    }

    $movimientos = $object->getAllMovimientos();
    include 'app/views/movimientos/viewMovimientos.php';
