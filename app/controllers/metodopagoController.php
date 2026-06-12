<?php

    use App\models\metodopagoModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new metodopagoModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_metodo'])) {
                $object->addMetodo($_POST['nombre_metodo']);
                header("Location: ?url=metodopago");
                exit();
            }
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_metodo'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $object->updateMetodo(
                    (int) $_POST['codigo_metodo'],
                    $_POST['nombre_metodo'],
                    $estado
                );
                header("Location: ?url=metodopago");
                exit();
            }
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteMetodo'])) {
                $result = $object->deleteMetodo((int) $_POST['codigo_metodo']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
        }

        header("Location: ?url=metodopago");
        exit();
    }

    $metodos = $object->getAllMetodos();
    // Carga la interfaz visual
    include 'app/views/metodopago/viewMetodoPago.php';