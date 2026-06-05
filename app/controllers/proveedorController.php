<?php

    use App\models\proveedorModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new proveedorModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rif_proveedor'])) {
                $object->addProveedor($_POST);
                header("Location: ?url=proveedor");
                exit();
            }
            header("Location: ?url=proveedor");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_proveedor'])) {
                $_POST['estado'] = isset($_POST['estado']) ? 1 : 0;
                $object->updateProveedor((int) $_POST['codigo_proveedor'], $_POST);
                header("Location: ?url=proveedor");
                exit();
            }
            header("Location: ?url=proveedor");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteProveedor'])) {
                $result = $object->deleteProveedor((int) $_POST['idproveedor']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=proveedor");
            exit();
        }

        else {
            header("Location: ?url=proveedor");
            exit();
        }
    }

    $proveedores = $object->getAllProveedores();
    include 'app/views/proveedores/viewProveedor.php';
