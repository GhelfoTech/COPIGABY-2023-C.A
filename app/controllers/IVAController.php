<?php

    use App\models\ivaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new ivaModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['porcentaje_iva'])) {
                $resultado = $object->addIva($_POST['porcentaje_iva']);
                $_SESSION['iva_flash'] = is_array($resultado) ? $resultado : ['status' => $resultado === true ? 'success' : 'error'];
                header("Location: ?url=iva");
                exit();
            }
            header("Location: ?url=iva");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_IVA'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $resultado = $object->updateIva((int) $_POST['codigo_IVA'], $_POST['porcentaje_iva'], $estado);
                $_SESSION['iva_flash'] = is_array($resultado) ? $resultado : ['status' => $resultado === true ? 'success' : 'error'];
                header("Location: ?url=iva");
                exit();
            }
            header("Location: ?url=iva");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteIva'])) {
                $result = $object->deleteIva((int) $_POST['codigo_IVA']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=iva");
            exit();
        }

        else {
            header("Location: ?url=iva");
            exit();
        }
    }

    $ivas = $object->getAllIvas();
    include 'app/views/IVA/viewIVA.php';
