<?php

    use App\models\servicioModel;
    use App\models\productoModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new servicioModel();
    $prodModel = new productoModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $materialesJson = $_POST['materiales'] ?? '[]';
                $materiales = is_string($materialesJson) ? json_decode($materialesJson, true) : null;
                $resultado = is_array($materiales)
                    ? $object->addServicio($_POST, $materiales)
                    : ['status' => 'error', 'message' => 'No se pudieron leer las cantidades de los materiales'];
                copigabyAjaxRespond($resultado, 'Servicio registrado correctamente.', 'No se pudo registrar el servicio.');
                setSystemFlashFromResult($resultado, 'Servicio registrado correctamente.', 'No se pudo registrar el servicio.');
                header("Location: ?url=servicio");
                exit();
            }
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idservicio'])) {
                $materialesJson = $_POST['materiales'] ?? '[]';
                $materiales = is_string($materialesJson) ? json_decode($materialesJson, true) : null;
                $_POST['estado'] = isset($_POST['estado']) ? 1 : 0;
                $resultado = is_array($materiales)
                    ? $object->updateServicio((int) $_POST['idservicio'], $_POST, $materiales)
                    : ['status' => 'error', 'message' => 'No se pudieron leer las cantidades de los materiales'];
                copigabyAjaxRespond($resultado, 'Servicio actualizado correctamente.', 'No se pudo actualizar el servicio.');
                setSystemFlashFromResult($resultado, 'Servicio actualizado correctamente.', 'No se pudo actualizar el servicio.');
                header("Location: ?url=servicio");
                exit();
            }
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteServicio'])) {
                $result = $object->deleteServicio((int)$_POST['idservicio']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            if (isset($_POST['getMaterials'])) {
                $result = $object->getMaterialesByServicio((int)$_POST['idservicio']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
        }
    }

    $servicios = $object->getAllServicios();
    $productos = $prodModel->getAllProducts(); // Para el selector de materiales
    
    include 'app/views/servicio/viewServicio.php';