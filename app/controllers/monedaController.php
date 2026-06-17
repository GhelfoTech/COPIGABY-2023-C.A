<?php

    use App\models\monedaModel;

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: ?url=login');
        exit();
    }

    $object = new monedaModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_moneda'])) {
                $result = $object->addMoneda([
                    'nombre_moneda' => trim((string) $_POST['nombre_moneda']),
                    'simbolo'       => trim((string) ($_POST['simbolo'] ?? '')),
                    'tasa_cambio'   => (float) ($_POST['tasa_cambio'] ?? 0),
                ]);
                $_SESSION['moneda_flash'] = $result;
                header('Location: ?url=moneda');
                exit();
            }
            header('Location: ?url=moneda');
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idmoneda'], $_POST['nombre_moneda'])) {
                $idMoneda = (int) $_POST['idmoneda'];
                if ($idMoneda <= 0) {
                    $_SESSION['moneda_flash'] = ['status' => 'error', 'message' => 'Identificador de moneda no válido.'];
                    header('Location: ?url=moneda');
                    exit();
                }

                $estado = isset($_POST['estado']) ? 1 : 0;
                $result = $object->updateMoneda($idMoneda, [
                    'nombre_moneda' => trim((string) $_POST['nombre_moneda']),
                    'simbolo'       => trim((string) ($_POST['simbolo'] ?? '')),
                    'tasa_cambio'   => (float) ($_POST['tasa_cambio'] ?? 0),
                    'estado'        => $estado,
                ]);
                $_SESSION['moneda_flash'] = $result;
                header('Location: ?url=moneda');
                exit();
            }
            header('Location: ?url=moneda');
            exit();
        }

        elseif ($_GET['type'] === 'setActive') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idmoneda'])) {
                $result = $object->setMonedaActiva((int) $_POST['idmoneda']);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($result);
                exit();
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida']);
            exit();
        }

        elseif ($_GET['type'] === 'details') {
            if (isset($_GET['id'])) {
                $moneda = $object->getMonedaById((int) $_GET['id']);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($moneda ?: ['status' => 'error', 'message' => 'Moneda no encontrada']);
                exit();
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'ID requerido']);
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteMoneda'])) {
                $result = $object->deleteMoneda((int) $_POST['idmoneda']);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($result);
                exit();
            }
            header('Location: ?url=moneda');
            exit();
        }

        else {
            header('Location: ?url=moneda');
            exit();
        }
    }

    $monedas       = $object->getAllMonedas();
    $monedaActiva  = $object->getMonedaActiva();
    $monedaFlash   = $_SESSION['moneda_flash'] ?? null;
    unset($_SESSION['moneda_flash']);

    include 'app/views/moneda/viewMoneda.php';
