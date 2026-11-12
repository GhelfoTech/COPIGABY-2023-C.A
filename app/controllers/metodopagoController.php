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
                $resultado = $object->addMetodo($_POST['nombre_metodo']);
                copigabyAjaxRespond($resultado, 'Método de pago registrado correctamente.', 'No se pudo registrar el método de pago.');
                $_SESSION['metodopago_flash'] = is_array($resultado) ? $resultado : ['status' => $resultado === true ? 'success' : 'error'];
                header("Location: ?url=metodopago");
                exit();
            }
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_metodo'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $resultado = $object->updateMetodo(
                    (int) $_POST['codigo_metodo'],
                    $_POST['nombre_metodo'],
                    $estado
                );
                copigabyAjaxRespond($resultado, 'Método de pago actualizado correctamente.', 'No se pudo actualizar el método de pago.');
                setSystemFlashFromResult($resultado, 'Método de pago actualizado correctamente.', 'No se pudo actualizar el método de pago.');
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