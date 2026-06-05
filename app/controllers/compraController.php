<?php

    use App\models\compraModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new compraModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['numero_factura_proveedor'])) {
                $_POST['codigo_usuario'] = $_SESSION['user_id'];
                $object->addCompra($_POST);
                header("Location: ?url=compra");
                exit();
            }
            header("Location: ?url=compra");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteCompra'])) {
                $result = $object->deleteCompra((int) $_POST['idcompra']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=compra");
            exit();
        }

        else {
            header("Location: ?url=compra");
            exit();
        }
    }

    $compras = $object->getAllCompras();
    $proveedores = $object->getProviders();
    include 'app/views/compras/viewCompra.php';
