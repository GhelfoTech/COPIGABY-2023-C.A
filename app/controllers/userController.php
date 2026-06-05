<?php

    use App\models\userModel;

    if (session_status() === PHP_SESSION_NONE) session_start();

    $object = new userModel();
    $error = ""; // Inicializamos la variable de error para la vista

    if (isset($_GET['type'])) {

        if ($_GET['type'] == 'register') {
            // Se ajustan los campos según la tabla 'usuario' del SQL proporcionado
            if (isset($_POST['cedula']) && isset($_POST['nombre_usuario']) && isset($_POST['telefono']) && isset($_POST['password'])) {
                $rol = $_POST['codigo_rol'] ?? 2; // Rol por defecto si no se envía
                $result = $object->addUser($_POST['cedula'], $_POST['nombre_usuario'], $_POST['telefono'], $_POST['password'], $rol);
                header("Location: ?url=user");
                exit();
            }
        }

        elseif ($_GET['type'] == 'update') {
            if (isset($_POST['idUser']) && isset($_POST['nombre_usuario'])) {
                $estado = isset($_POST['estado']) ? 1 : 0;
                $object->updateUser(
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
                // idUser mapeado a codigo_usuario
                $result = $object->deleteUser($_POST["idUser"]); 
                echo json_encode($result);
                die();
            }
            $usuarios = $object->getAllUsers();
            $roles = $object->getRoles();
            include 'app/views/user/index.php';
        }
        
        else {
            $usuarios = $object->getAllUsers();
            $roles = $object->getRoles();
            include 'app/views/user/index.php';
        }

    } else {
        $usuarios = $object->getAllUsers();
        $roles = $object->getRoles();
        include 'app/views/user/index.php';
    }