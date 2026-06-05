<?php

    use App\models\medidaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    // Seguridad: verificar sesión
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new medidaModel();

    if (isset($_GET['type'])) {

        // Procesar Registro
        if ($_GET['type'] == 'register') {
            if (isset($_POST['nombre'])) {
                $object->addMedida($_POST['nombre']);
                header("Location: ?url=medida");
                exit();
            }
        }

        // Procesar Actualización
        elseif ($_GET['type'] == 'update') {
            if (isset($_POST['codigo_media'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $object->updateMedida($_POST['codigo_media'], $_POST['nombre'], $estado);
                header("Location: ?url=medida");
                exit();
            }
        }

        // Acciones AJAX y Vista Principal
        elseif ($_GET['type'] == 'main') {
            if(isset($_POST["deleteMedida"])) {
                $result = $object->deleteMedida($_POST["codigo_media"]); 
                echo json_encode($result);
                die();
            }
            $medidas = $object->getAllMedidas();
            include 'app/views/medida/index.php';
        }
        
        else {
            $medidas = $object->getAllMedidas();
            include 'app/views/medida/index.php';
        }

    } else {
        // Acción por defecto
        $medidas = $object->getAllMedidas();
        include 'app/views/medida/index.php';
    }
?>