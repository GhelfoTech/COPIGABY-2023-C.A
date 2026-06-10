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
                $_POST['cedula_usuario'] = $_SESSION['user_id'];
                $items = json_decode($_POST['items'], true);
                $object->addCompra($_POST, $items);
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

        elseif ($_GET['type'] === 'details') {
            if (isset($_GET['id'])) {
                $header = $object->getCompraById((int)$_GET['id']);
                $items = $object->getItemsByCompra((int)$_GET['id']);
                header('Content-Type: application/json');
                echo json_encode(['header' => $header, 'items' => $items]);
                exit();
            }
        }

        else {
            header("Location: ?url=compra");
            exit();
        }
    }

    $compras = $object->getAllCompras();
    $proveedores = $object->getProviders();
    $productos = $object->getProducts();
    include 'app/views/compras/viewCompra.php';
