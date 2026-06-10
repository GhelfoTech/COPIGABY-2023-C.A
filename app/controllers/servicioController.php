<?php

    use App\models\servicioModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new servicioModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_servicio'])) {
                $materiales = json_decode($_POST['materiales_json'] ?? '[]', true);
                $result = $object->addService($_POST, $materiales);
                $_SESSION['flash'] = $result;
                header("Location: ?url=servicio");
                exit();
            }
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_servicio'])) {
                $id = (int) $_POST['codigo_servicio'];
                $_POST['estado'] = isset($_POST['estado']) ? 1 : 0;
                $materiales = json_decode($_POST['materiales_json'] ?? '[]', true);
                $result = $object->updateService($id, $_POST, $materiales);
                $_SESSION['flash'] = $result;
                header("Location: ?url=servicio");
                exit();
            }
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteService'])) {
                $result = $object->deleteService((int) $_POST['idservicio']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
        }

        elseif ($_GET['type'] === 'getDetails') {
            if (isset($_GET['id'])) {
                $data = $object->getServiceWithMaterials((int)$_GET['id']);
                header('Content-Type: application/json');
                echo json_encode($data);
                exit();
            }
        }

        header("Location: ?url=servicio");
        exit();
    }

    // Carga de datos para la vista principal
    $servicios = $object->getAllServices();
    $productos = $object->getProductosDisponibles();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    include 'app/views/servicio/viewServicio.php';
?>