<?php

    use App\models\monedaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    // Verificación de sesión para asegurar que solo usuarios autenticados accedan
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new monedaModel();

    if (isset($_GET['type'])) {

        // Acción para registrar una nueva moneda
        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_moneda'])) {
                $object->addMoneda($_POST);
                header("Location: ?url=moneda");
                exit();
            }
        }

        // Acción para actualizar datos de una moneda existente
        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idmoneda'], $_POST['nombre_moneda'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $object->updateMoneda((int) $_POST['idmoneda'], [
                    'nombre_moneda' => trim($_POST['nombre_moneda']),
                    'simbolo'       => trim($_POST['simbolo']),
                    'tasa_cambio'   => (float) $_POST['tasa_cambio'],
                    'estado'        => $estado,
                ]);
                header("Location: ?url=moneda");
                exit();
            }
        }

        // Acciones principales (vía AJAX para eliminación lógica)
        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteMoneda'])) {
                $result = $object->deleteMoneda((int) $_POST['idmoneda']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
        }
    }

    // Carga predeterminada: obtiene todas las monedas y carga la interfaz
    $monedas = $object->getAllMonedas();
    include 'app/views/moneda/viewMoneda.php';