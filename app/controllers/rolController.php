<?php

    use App\models\rolModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new rolModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_rol'])) {
                $object->addRol($_POST);
                header("Location: ?url=rol");
                exit();
            }
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_rol'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $object->updateRol((int)$_POST['codigo_rol'], [
                    'nombre_rol'  => $_POST['nombre_rol'],
                    'descripcion' => $_POST['descripcion'],
                    'estado'      => $estado
                ]);
                header("Location: ?url=rol");
                exit();
            }
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteRol'])) {
                $result = $object->deleteRol((int) $_POST['idrol']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
        }

        header("Location: ?url=rol");
        exit();
    }

    $roles = $object->getAllRoles();
    // Carga de la vista
    include 'app/views/rol/viewRol.php';