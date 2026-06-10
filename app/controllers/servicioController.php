<?php

    use App\models\servicioModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    // Seguridad básica: Verificar sesión
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new servicioModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_servicio'])) {
                $object->addService($_POST);
                header("Location: ?url=servicio");
                exit();
            }
            header("Location: ?url=servicio");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_servicio'])) {
                $_POST['estado'] = isset($_POST['estado']) ? 1 : 0;
                $object->updateService((int) $_POST['codigo_servicio'], $_POST);
                header("Location: ?url=servicio");
                exit();
            }
            header("Location: ?url=servicio");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteService'])) {
                $result = $object->deleteService((int) $_POST['idservicio']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=servicio");
            exit();
        }

        else {
            header("Location: ?url=servicio");
            exit();
        }
    }

    // Carga por defecto de la lista
    $servicios = $object->getAllServices();
    include 'app/views/servicio/viewServicio.php';
?>