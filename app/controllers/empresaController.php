<?php

    use App\models\empresaModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new empresaModel();

    if (isset($_GET['type'])) {

        if ($_GET['type'] === 'register') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rif_empresa'])) {
                $object->addEmpresa([
                    'rif_empresa'    => trim($_POST['rif_empresa']),
                    'nombre_empresa' => trim($_POST['nombre_empresa']),
                    'telefono'       => trim($_POST['telefono']),
                    'correo'         => trim($_POST['correo']),
                    'direccion'      => trim($_POST['direccion']),
                    'logo'           => trim($_POST['logo'] ?? ''),
                ]);
                header("Location: ?url=empresa");
                exit();
            }
            header("Location: ?url=empresa");
            exit();
        }

        elseif ($_GET['type'] === 'update') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_empresa'])) {
                $object->updateEmpresa((int) $_POST['codigo_empresa'], [
                    'rif_empresa'    => trim($_POST['rif_empresa']),
                    'nombre_empresa' => trim($_POST['nombre_empresa']),
                    'telefono'       => trim($_POST['telefono']),
                    'correo'         => trim($_POST['correo']),
                    'direccion'      => trim($_POST['direccion']),
                    'logo'           => trim($_POST['logo'] ?? ''),
                ]);
                header("Location: ?url=empresa");
                exit();
            }
            header("Location: ?url=empresa");
            exit();
        }

        elseif ($_GET['type'] === 'main') {
            if (isset($_POST['deleteEmpresa'])) {
                $result = $object->deleteEmpresa((int) $_POST['codigo_empresa']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
            header("Location: ?url=empresa");
            exit();
        }

        elseif ($_GET['type'] === 'details') {
            if (isset($_GET['id'])) {
                $result = $object->getEmpresaById((int) $_GET['id']);
                header('Content-Type: application/json');
                echo json_encode($result);
                exit();
            }
        }

        else {
            header("Location: ?url=empresa");
            exit();
        }
    }

    $empresas = $object->getAllEmpresas();
    include 'app/views/empresa/viewEmpresa.php';
