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

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idcompra'], $_POST['codigo_proveedor'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $object->updateCompra((int) $_POST['idcompra'], [
                    'codigo_proveedor'          => (int) $_POST['codigo_proveedor'],
                    'numero_factura_proveedor'  => trim($_POST['numero_factura_proveedor']),
                    'fecha_compra'              => $_POST['fecha_compra'],
                    'monto_total'               => $_POST['monto_total'],
                    'estado'                    => $estado,
                ]);
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
