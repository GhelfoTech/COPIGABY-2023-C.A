<?php

    use App\models\userModel;
    use App\models\rolModel;

    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: ?url=login");
        exit();
    }

    $object = new userModel();
    $rolModel = new rolModel();
    $error = ""; // Inicializamos la variable de error para la vista

    if (isset($_GET['type'])) {

        if ($_GET['type'] == 'register') {
            if (isset($_POST['cedula']) && isset($_POST['nombre_usuario']) && isset($_POST['telefono']) && isset($_POST['password'])) {
                $rol = $_POST['codigo_rol'] ?? 2; // Rol por defecto si no se envía
                $result = $object->addUser($_POST['cedula'], $_POST['nombre_usuario'], $_POST['telefono'], $_POST['password'], $rol);
                header("Location: ?url=user");
                exit();
            }
        }

        elseif ($_GET['type'] == 'update') {
            if (isset($_POST['idUser']) && isset($_POST['nombre_usuario'])) {
                $estado = isset($_POST['estado']) && $_POST['estado'] == 'on' ? 1 : 0; 
                $object->updateUser( // idUser ahora es codigo_usuario
                    $_POST['idUser'], 
                    $_POST['cedula'], 
                    $_POST['nombre_usuario'], 
                    $_POST['telefono'], 
                    $_POST['codigo_rol'], 
                    $estado
                );
                header("Location: ?url=user");
                exit();
            }
        }

        elseif ($_GET['type'] == 'main') {
            if(isset($_POST["deleteUser"])) {
                $result = $object->deleteUser((int)$_POST["idUser"]); // idUser es codigo_usuario
                echo json_encode($result);
                die();
            }
        }

        elseif ($_GET['type'] == 'credenciales') {
            header('Content-Type: application/json');
            $passwordActual = trim((string)($_POST['password_actual'] ?? ''));
            $nuevoNombre = trim((string)($_POST['nuevo_nombre'] ?? ''));
            $nuevaPassword = trim((string)($_POST['nueva_password'] ?? ''));
            $confirmarPassword = trim((string)($_POST['confirmar_password'] ?? ''));

            $result = $object->updateCredentials(
                $_SESSION['username'],
                $passwordActual,
                $nuevoNombre,
                $nuevaPassword,
                $confirmarPassword
            );

            if ($result['status'] === 'success') {
                $_SESSION['username'] = $nuevoNombre ?: $_SESSION['username'];
            }

            echo json_encode($result);
            die();
        }
    }

    // Carga de datos común para la vista (se ejecuta si no hubo redirección o die)
    $usuarios = $object->getAllUsers();
    $roles = $rolModel->getAllRoles();
    include 'app/views/usuario/viewUser.php';