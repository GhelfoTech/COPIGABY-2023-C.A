<?php

    use App\models\medidaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new medidaModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
                $abreviatura = trim($_POST['abreviatura'] ?? '');
                $object->addMedida($_POST['nombre'], $abreviatura ?: null);
                header("Location: ?url=medida");
                exit();
            }
            header("Location: ?url=medida");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_media'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $abreviatura = trim($_POST['abreviatura'] ?? '');
                $object->updateMedida((int) $_POST['codigo_media'], $_POST['nombre'], $abreviatura ?: null, $estado);
                header("Location: ?url=medida");
                exit();
            }
            header("Location: ?url=medida");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteMedida'])) {
                $result = $object->deleteMedida((int) $_POST['codigo_media']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=medida");
            exit();
        }

        else {
            header("Location: ?url=medida");
            exit();
        }
    }

    $medidas = $object->getAllMedidas();
    include 'app/views/medida/viewMedida.php';
