<?php

    use App\models\compraModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new compraModel();

    if (isset($_GET['type'])) {

        // Registro de compra
        if ($_GET['type'] == 'register') {
            if (isset($_POST['numero_factura_proveedor'])) {
                // Asignamos el usuario que está logueado como el comprador
                $_POST['codigo_usuario'] = $_SESSION['user_id'];
                $object->addCompra($_POST);
                header("Location: ?url=compra");
                exit();
            }
        }

        // Acciones AJAX (Eliminar/Anular)
        elseif ($_GET['type'] == 'main') {
            if(isset($_POST["deleteCompra"])) {
                $result = $object->deleteCompra($_POST["idcompra"]); 
                echo json_encode($result);
                die();
            }
            $compras = $object->getAllCompras();
            $proveedores = $object->getProviders();
            include 'app/views/compras/index.php';
        }
        
        else {
            $compras = $object->getAllCompras();
            $proveedores = $object->getProviders();
            include 'app/views/compras/index.php';
        }

    } else {
        // Acción por defecto
        $compras = $object->getAllCompras();
        $proveedores = $object->getProviders();
        include 'app/views/compras/index.php';
    }
?>