<?php

    use App\models\ivaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    // Seguridad: verificar sesión
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new ivaModel();

    if (isset($_GET['type'])) {

        // Procesar Registro
        if ($_GET['type'] == 'register') {
            if (isset($_POST['porcentaje_iva'])) {
                $object->addIva($_POST['porcentaje_iva']);
                header("Location: ?url=iva");
                exit();
            }
        }

        // Procesar Actualización
        elseif ($_GET['type'] == 'update') {
            if (isset($_POST['codigo_IVA'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $object->updateIva($_POST['codigo_IVA'], $_POST['porcentaje_iva'], $estado);
                header("Location: ?url=iva");
                exit();
            }
        }

        // Acciones AJAX y Vista Principal
        elseif ($_GET['type'] == 'main') {
            if(isset($_POST["deleteIva"])) {
                $result = $object->deleteIva($_POST["codigo_IVA"]); 
                echo json_encode($result);
                die();
            }
            $ivas = $object->getAllIvas();
            include 'app/views/iva/index.php';
        }
        
        else {
            $ivas = $object->getAllIvas();
            include 'app/views/iva/index.php';
        }

    } else {
        // Acción por defecto
        $ivas = $object->getAllIvas();
        include 'app/views/iva/index.php';
    }