<?php

    use App\models\proveedorModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    // Seguridad: verificar sesión
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new proveedorModel();

    if (isset($_GET['type'])) {

        // Procesar Registro
        if ($_GET['type'] == 'register') {
            if (isset($_POST['rif_proveedor'])) {
                $object->addProveedor($_POST);
                header("Location: ?url=proveedor");
                exit();
            }
        }

        // Procesar Actualización
        elseif ($_GET['type'] == 'update') {
            if (isset($_POST['codigo_proveedor'])) {
                $_POST['estado'] = isset($_POST['estado']) ? 1 : 0;
                $object->updateProveedor($_POST['codigo_proveedor'], $_POST);
                header("Location: ?url=proveedor");
                exit();
            }
        }

        // Acciones AJAX y Vista Principal
        elseif ($_GET['type'] == 'main') {
            if(isset($_POST["deleteProveedor"])) {
                $result = $object->deleteProveedor($_POST["idproveedor"]); 
                echo json_encode($result);
                die();
            }
            $proveedores = $object->getAllProveedores();
            include 'app/views/proveedores/viewProveedor.php';
        }
        
        else {
            $proveedores = $object->getAllProveedores();
            include 'app/views/proveedores/viewProveedor.php';
        }

    } else {
        // Acción por defecto
        $proveedores = $object->getAllProveedores();
        include 'app/views/proveedores/viewProveedor.php';
    }
?>